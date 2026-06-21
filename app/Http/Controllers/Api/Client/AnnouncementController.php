<?php

namespace Ruff\Http\Controllers\Api\Client;

use Ruff\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Ruff\Http\Controllers\Controller;

class AnnouncementController extends Controller
{
    /**
     * Returns the enabled dashboard announcements, shaped for the client
     * dashboard cards (the presentation metadata is derived from the type).
     */
    public function __invoke(): JsonResponse
    {
        $data = Announcement::query()
            ->where('enabled', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(function (Announcement $announcement) {
                $presentation = $announcement->presentation();

                return [
                    'tag' => $presentation['tag'],
                    'cls' => $presentation['cls'],
                    'offer' => $presentation['offer'],
                    'title' => $announcement->title,
                    'body' => $announcement->body,
                    'link' => $announcement->link,
                ];
            });

        return new JsonResponse(['data' => $data]);
    }
}
