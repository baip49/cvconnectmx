<?php

namespace App\Notifications;

use Illuminate\Support\Str;
use NotificationChannels\Telegram\Enums\ParseMode;
use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Health\Checks\Result;
use Spatie\Health\Notifications\CheckFailedNotification;

class HealthCheckTelegramNotification extends CheckFailedNotification
{
    public function toTelegram(object $notifiable): TelegramMessage
    {
        $params = $this->transParameters();
        $header = sprintf(
            '🚨 Health checks alert for %s (%s)',
            $params['application_name'],
            $params['env_name']
        );

        $lines = ['<b>'.$this->escapeHtml($header).'</b>', ''];

        foreach ($this->results as $result) {
            $lines[] = $this->formatResultLine($result);
        }

        return TelegramMessage::create()
            ->parseMode(ParseMode::HTML)
            ->content(implode("\n", $lines));
    }

    private function formatResultLine(Result $result): string
    {
        $statusValue = Str::lower((string) $result->status->value);
        $status = Str::upper($statusValue);
        $label = $this->escapeHtml($result->check->getLabel());
        $notificationMessage = $this->escapeHtml($result->getNotificationMessage());
        $emoji = $this->statusEmoji($statusValue);

        return sprintf('%s <b>%s</b> %s: %s', $emoji, $label, $status, $notificationMessage);
    }

    private function statusEmoji(string $status): string
    {
        return match ($status) {
            'failed', 'crashed' => '🚨',
            'warning' => '⚠️',
            'ok' => '✅',
            default => 'ℹ️',
        };
    }

    private function escapeHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
