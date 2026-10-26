<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Email copy for the admins of an operations alert that also goes to Telegram (backups, server resources).
 */
class SystemAlertMail extends Notification
{
    public function __construct(public string $telegramHtml) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Telegram messages are a few HTML lines; the first one is the headline. "u": emojis contain bytes \R would split on.
        $lines = collect(preg_split('/\R/u', $this->telegramHtml))
            ->map(fn (string $line): string => trim(html_entity_decode(strip_tags($line), ENT_QUOTES | ENT_HTML5, 'UTF-8')))
            ->filter()
            ->values();

        $mail = (new MailMessage)
            ->subject(Str::limit((string) $lines->first(), 120))
            ->greeting((string) $lines->first());

        foreach ($lines->skip(1) as $line) {
            $mail->line($line);
        }

        return $mail->action(__('notifications.view'), url('/admin'));
    }
}
