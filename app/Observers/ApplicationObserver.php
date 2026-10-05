<?php

namespace App\Observers;

use App\Models\Application;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ApplicationObserver
{
    public function created(Application $application): void
    {
        $companyUser = $application->vacancy?->company?->user;
        $candidateName = $application->candidate?->user?->name ?? 'Un candidato';
        $vacancyTitle = $application->vacancy?->title ?? 'una vacante';

        if ($companyUser) {
            Notification::make()
                ->title('Nueva postulación recibida')
                ->body("{$candidateName} aplicó a \"{$vacancyTitle}\".")
                ->icon('heroicon-o-paper-airplane')
                ->actions([
                    Action::make('view')
                        ->label('Ver')
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
        $vacancyTitle = $application->vacancy?->title ?? 'una vacante';

        $statusLabels = [
            'pending' => 'pendiente',
            'interview' => 'en entrevista',
            'accepted' => 'aceptada',
            'rejected' => 'rechazada',
            'offered' => 'con oferta',
        ];

        if ($candidateUser) {
            Notification::make()
                ->title('Tu postulación cambió de estado')
                ->body("Tu postulación a \"{$vacancyTitle}\" ahora está ".($statusLabels[$application->status] ?? $application->status).'.')
                ->icon('heroicon-o-bell')
                ->actions([
                    Action::make('view')
                        ->label('Ver')
                        ->url(url('/dashboard')),
                ])
                ->sendToDatabase($candidateUser);
        }
    }
}
