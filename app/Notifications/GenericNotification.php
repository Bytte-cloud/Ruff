<?php

namespace Ruff\Notifications;

use Illuminate\Notifications\Notification;

/**
 * A simple, persisted in-app notification surfaced in the client dashboard's
 * notification center. Categories drive the icon/colour on the frontend
 * (event, billing, maintenance, system).
 */
class GenericNotification extends Notification
{
    public function __construct(
        public string $category,
        public string $title,
        public string $message,
        public ?string $actionUrl = null,
    ) {
    }

    /**
     * Deliver to the database channel so it shows up in the notification center.
     */
    public function via(): array
    {
        return ['database'];
    }

    public function toDatabase(): array
    {
        return [
            'category' => $this->category,
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ];
    }
}
