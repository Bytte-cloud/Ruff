<?php

namespace Ruff\Console\Commands\Notifications;

use Ruff\Models\User;
use Illuminate\Console\Command;
use Ruff\Notifications\GenericNotification;

class SendNotificationCommand extends Command
{
    protected $signature = 'ruff:notify
        {message : The body of the notification.}
        {--title=Notice : The notification title.}
        {--category=maintenance : event | billing | maintenance | system.}
        {--url= : Optional URL the notification links to.}
        {--user= : Send to a single user (id, email or username) instead of everyone.}';

    protected $description = 'Send an in-app notification to all users (or one), e.g. a maintenance announcement.';

    public function handle(): int
    {
        $make = fn () => new GenericNotification(
            $this->option('category'),
            $this->option('title'),
            $this->argument('message'),
            $this->option('url') ?: null,
        );

        $target = $this->option('user');
        if ($target) {
            $user = User::query()
                ->where('id', $target)
                ->orWhere('email', $target)
                ->orWhere('username', $target)
                ->first();

            if (!$user) {
                $this->error('No user found matching ' . $target . '.');

                return 1;
            }

            $user->notify($make());
            $this->info('Notification sent to ' . $user->username . '.');

            return 0;
        }

        $count = 0;
        User::query()->chunkById(200, function ($users) use ($make, &$count) {
            foreach ($users as $user) {
                $user->notify($make());
                $count++;
            }
        });

        $this->info('Notification broadcast to ' . $count . ' user(s).');

        return 0;
    }
}
