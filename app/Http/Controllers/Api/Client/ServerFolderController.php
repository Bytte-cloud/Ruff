<?php

namespace Ruff\Http\Controllers\Api\Client;

use Ruff\Models\ServerFolder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Ruff\Http\Requests\Api\Client\ClientApiRequest;
use Ruff\Transformers\Api\Client\ServerFolderTransformer;

class ServerFolderController extends ClientApiController
{
    /**
     * Returns all the folders the authenticated user has created, each with the
     * list of server UUIDs filed under it.
     */
    public function index(ClientApiRequest $request): array
    {
        $folders = $request->user()->serverFolders()->with('servers:id,uuid')->get();

        return $this->fractal->collection($folders)
            ->transformWith($this->getTransformer(ServerFolderTransformer::class))
            ->toArray();
    }

    /**
     * Creates a new folder for the authenticated user.
     */
    public function store(ClientApiRequest $request): array
    {
        $data = $this->validate($request, [
            'name' => ['required', 'string', 'max:191'],
            'color' => ['nullable', 'string', 'max:32'],
            'sort' => ['sometimes', 'integer', 'min:0'],
        ]);

        $folder = $request->user()->serverFolders()->create([
            'name' => $data['name'],
            'color' => $data['color'] ?? null,
            'sort' => $data['sort'] ?? 0,
        ]);

        return $this->fractal->item($folder->loadMissing('servers:id,uuid'))
            ->transformWith($this->getTransformer(ServerFolderTransformer::class))
            ->toArray();
    }

    /**
     * Updates an existing folder (rename / recolor / reorder).
     */
    public function update(ClientApiRequest $request, string $folder): array
    {
        $model = $request->user()->serverFolders()->where('uuid', $folder)->firstOrFail();

        $data = $this->validate($request, [
            'name' => ['sometimes', 'required', 'string', 'max:191'],
            'color' => ['nullable', 'string', 'max:32'],
            'sort' => ['sometimes', 'integer', 'min:0'],
        ]);

        $model->update($data);

        return $this->fractal->item($model->loadMissing('servers:id,uuid'))
            ->transformWith($this->getTransformer(ServerFolderTransformer::class))
            ->toArray();
    }

    /**
     * Deletes a folder. The servers themselves are untouched — only the folder
     * and its assignments (via the cascading pivot) are removed.
     */
    public function delete(ClientApiRequest $request, string $folder): JsonResponse
    {
        $model = $request->user()->serverFolders()->where('uuid', $folder)->firstOrFail();
        $model->delete();

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Files a server into a folder, or removes it from all folders when no
     * folder is provided. A server can live in at most one of a user's folders,
     * so any prior assignment is cleared first.
     */
    public function assign(ClientApiRequest $request): JsonResponse
    {
        $data = $this->validate($request, [
            'server' => ['required', 'string'],
            'folder' => ['nullable', 'string'],
        ]);

        $server = $request->user()->accessibleServers()
            ->where('servers.uuid', $data['server'])
            ->first();

        if (is_null($server)) {
            return new JsonResponse(['errors' => [['detail' => 'Server not found.']]], JsonResponse::HTTP_NOT_FOUND);
        }

        // Clear any existing assignment within this user's folders so a server
        // only ever appears in one folder.
        $folderIds = $request->user()->serverFolders()->pluck('id');
        DB::table('server_folder_servers')
            ->whereIn('folder_id', $folderIds)
            ->where('server_id', $server->id)
            ->delete();

        if (!empty($data['folder'])) {
            $folder = $request->user()->serverFolders()->where('uuid', $data['folder'])->firstOrFail();
            $folder->servers()->attach($server->id);
        }

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}
