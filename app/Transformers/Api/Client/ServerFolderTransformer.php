<?php

namespace Ruff\Transformers\Api\Client;

use Ruff\Models\ServerFolder;

class ServerFolderTransformer extends BaseClientTransformer
{
    public function getResourceName(): string
    {
        return ServerFolder::RESOURCE_NAME;
    }

    /**
     * Returns a server folder in an API response format. The `servers` array is
     * the list of server UUIDs filed under this folder, which the dashboard
     * uses to group the server list it already fetches.
     */
    public function transform(ServerFolder $model): array
    {
        return [
            'uuid' => $model->uuid,
            'name' => $model->name,
            'color' => $model->color,
            'sort' => $model->sort,
            'servers' => $model->servers->pluck('uuid')->all(),
            'created_at' => $model->created_at->toAtomString(),
            'updated_at' => $model->updated_at->toAtomString(),
        ];
    }
}
