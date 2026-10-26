<?php

namespace App\Observers;

use App\Models\Application;
use App\Models\User;
use App\Notifications\ActivityMail;

/**
 * Tells the other side of an application what happened: the company about applications and offer answers,
 * the candidate about offers and status changes. Each notice goes to the bell and by email.
 */
class ApplicationObserver
{
    public function created(Application $application): void
    {
        if ($application->is_offer) {
            $this->notify($application->candidate?->user, 'offer_received', $application, url('/dashboard/offers'), 'heroicon-o-briefcase');

            return;
        }

        $this->notify($application->vacancy?->company?->user, 'received', $application, url("/company/applications/{$application->id}"), 'heroicon-o-paper-airplane');
    }

    public function updated(Application $application): void
    {
        if (! $application->wasChanged('status')) {
            return;
        }

        // The candidate answering an offer is news for the company, not for the candidate.
        if ($application->is_offer && in_array($application->status, ['accepted', 'rejected'], true)) {
            $event = $application->status === 'accepted' ? 'offer_accepted' : 'offer_declined';
            $this->notify($application->vacancy?->company?->user, $event, $application, url("/company/applications/{$application->id}"), 'heroicon-o-briefcase');

            return;
        }

        $event = $application->status === 'offered' ? 'offer_received' : 'status_changed';
        $this->notify($application->candidate?->user, $event, $application, url($event === 'offer_received' ? '/dashboard/offers' : '/dashboard'), 'heroicon-o-bell');
    }

    /**
     * Notices are stored already written, so each one uses its recipient's language.
     */
    protected function notify(?User $user, string $event, Application $application, string $url, string $icon): void
    {
        if (! $user) {
            return;
        }

        $locale = $user->locale ?? config('app.locale');
        $replace = [
            'name' => $application->candidate?->user?->name ?? __('notifications.applications.a_candidate', [], $locale),
            'company' => $application->vacancy?->company?->name ?? '',
            'vacancy' => $application->vacancy?->title ?? __('notifications.applications.a_vacancy', [], $locale),
            'status' => __("notifications.applications.statuses.{$application->status}", [], $locale),
        ];
        ActivityMail::deliver(
            $user,
            __("notifications.applications.{$event}.title", [], $locale),
            __("notifications.applications.{$event}.body", $replace, $locale),
            $url,
            $icon,
        );
    }
}
