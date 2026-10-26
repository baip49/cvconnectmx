<?php

namespace App\Observers;

use App\Models\Application;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/**
 * Notifications are stored already written, so each one uses its recipient's language.
 */
class ApplicationObserver
{
    public function created(Application $application): void
    {
        $companyUser = $application->vacancy?->company?->user;

        if ($companyUser) {
            $locale = $companyUser->locale ?? config('app.locale');

            Notification::make()
                ->title(__('notifications.applications.received.title', [], $locale))
                ->body(__('notifications.applications.received.body', [
                    'name' => $application->candidate?->user?->name ?? __('notifications.applications.a_candidate', [], $locale),
                    'vacancy' => $application->vacancy?->title ?? __('notifications.applications.a_vacancy', [], $locale),
                ], $locale))
                ->icon('heroicon-o-paper-airplane')
                ->actions([
                    Action::make('view')
                        ->label(__('notifications.view', [], $locale))
                        ->url(url("/company/applications/{$application->id}")),
                ])
                ->sendToDatabase($companyUser);
        }
    }

    public function updated(Application $application): void
    {
        if (! $application->wasChanged('status')) {
            return;
        }

        $candidateUser = $application->candidate?->user;

        if ($candidateUser) {
            $locale = $candidateUser->locale ?? config('app.locale');

            Notification::make()
                ->title(__('notifications.applications.status_changed.title', [], $locale))
                ->body(__('notifications.applications.status_changed.body', [
                    'vacancy' => $application->vacancy?->title ?? __('notifications.applications.a_vacancy', [], $locale),
                    'status' => __("notifications.applications.statuses.{$application->status}", [], $locale),
                ], $locale))
                ->icon('heroicon-o-bell')
                ->actions([
                    Action::make('view')
                        ->label(__('notifications.view', [], $locale))
                        ->url(url('/dashboard')),
                ])
                ->sendToDatabase($candidateUser);
        }
    }
}
