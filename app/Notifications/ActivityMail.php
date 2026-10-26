<?php

namespace App\Notifications;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as BellNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Email copy of an in-app notification (tickets, applications, offers, account verification).
 * Title and body arrive already written in the recipient's language.
 */
class ActivityMail extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public string $url,
    ) {}

    /**
     * Sends the same notice to the user's bell and by email.
     */
    public static function deliver(User $user, string $title, string $body, string $url, string $icon): void
    {
        BellNotification::make()
            ->title($title)
            ->body($body)
            ->icon($icon)
            ->actions([
                Action::make('view')
                    ->label(__('notifications.view', [], $user->locale ?? config('app.locale')))
                    ->url($url),
            ])
            ->sendToDatabase($user);

        $user->notify(new self($title, $body, $url));
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting($this->title)
            ->line($this->body)
            ->action(__('notifications.view'), $this->url);
    }
}
