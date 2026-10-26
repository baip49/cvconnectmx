<?php

namespace App\Notifications;

use App\Models\User;
use Spatie\Health\Notifications\Notifiable as HealthNotifiable;

class HealthCheckNotifiable extends HealthNotifiable
{
    public function routeNotificationForTelegram(): string
    {
        $chatId = config('services.telegram.chat_id');

        return is_scalar($chatId) ? (string) $chatId : '';
    }

    /**
     * Health alerts go by email to every admin, alongside Telegram.
     *
     * @return array<int, string>
     */
    public function routeNotificationForMail(): array
    {
        return User::query()->admins()->pluck('email')->all();
    }

    public function getKey(): int
    {
        return 1;
    }
}
