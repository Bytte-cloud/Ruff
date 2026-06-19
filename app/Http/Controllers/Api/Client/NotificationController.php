<?php

namespace Ruff\Http\Controllers\Api\Client;

use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;
use Ruff\Http\Requests\Api\Client\ClientApiRequest;

class NotificationController extends ClientApiController
{
    /**
     * Returns the most recent notifications for the authenticated user along
     * with the number that are still unread.
     */
    public function index(ClientApiRequest $request): array
    {
        $user = $request->user();

        $items = $user->notifications()
            ->limit(50)
            ->get()
            ->map(function (DatabaseNotification $notification) {
                $data = $notification->data;

                return [
                    'object' => 'notification',
                    'attributes' => [
                        'id' => $notification->id,
                        'category' => $data['category'] ?? 'system',
                        'title' => $data['title'] ?? '',
                        'message' => $data['message'] ?? '',
                        'action_url' => $data['action_url'] ?? null,
                        'read' => $notification->read_at !== null,
                        'created_at' => $notification->created_at->toAtomString(),
                    ],
                ];
            });

        return [
            'object' => 'list',
            'data' => $items,
            'meta' => [
                'unread_count' => $user->unreadNotifications()->count(),
            ],
        ];
    }

    /**
     * Marks a single notification as read.
     */
    public function read(ClientApiRequest $request, string $notification): JsonResponse
    {
        $model = $request->user()->notifications()->where('id', $notification)->first();
        $model?->markAsRead();

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Marks every unread notification as read.
     */
    public function readAll(ClientApiRequest $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Deletes a single notification.
     */
    public function delete(ClientApiRequest $request, string $notification): JsonResponse
    {
        $request->user()->notifications()->where('id', $notification)->delete();

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}
