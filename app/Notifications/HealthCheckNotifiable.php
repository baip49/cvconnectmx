<?php

namespace App\Notifications;

use Spatie\Health\Notifications\Notifiable as HealthNotifiable;

class HealthCheckNotifiable extends HealthNotifiable
{
    public function routeNotificationForTelegram(): string
    {
        $chatId = config('services.telegram.chat_id');

        return is_scalar($chatId) ? (string) $chatId : '';
    }

    public function getKey(): int
    {
        return 1;
    }
}
