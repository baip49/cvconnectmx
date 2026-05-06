<?php

namespace App\Filament\Candidate\Resources\Vacancies\Pages;

use App\Filament\Candidate\Resources\Vacancies\VacancyResource;
use App\Models\Vacancy;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewVacancy extends ViewRecord
{
    protected static string $resource = VacancyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('accept_offer')
                ->label('Aceptar Oferta')
                ->color('success')
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->modalHeading('Aceptar Oferta de Trabajo')
                ->modalDescription('¿Estás seguro de que deseas aceptar esta oferta? La empresa será notificada de inmediato.')
                ->modalSubmitActionLabel('Sí, aceptar oferta')
                ->visible(fn (Vacancy $record) => 
                    $record->applications()
                        ->where('candidate_id', Auth::user()->candidate?->id)
                        ->where('is_offer', true)
                        ->where('status', 'offered')
                        ->exists()
                )
                ->action(function (Vacancy $record) {
                    $record->applications()
                        ->where('candidate_id', Auth::user()->candidate->id)
                        ->where('is_offer', true)
                        ->update(['status' => 'accepted']);

                    Notification::make()
                        ->title('Oferta aceptada')
                        ->body("Has aceptado la oferta para {$record->title}. ¡Felicidades!")
                        ->success()
                        ->send();
                }),

            Action::make('reject_offer')
                ->label('Rechazar Oferta')
                ->color('danger')
                ->icon('heroicon-o-x-mark')
                ->requiresConfirmation()
                ->modalHeading('Rechazar Oferta')
                ->modalDescription('¿Estás seguro de que deseas rechazar esta oferta? Esta acción no se puede deshacer.')
                ->modalSubmitActionLabel('Sí, rechazar')
                ->visible(fn (Vacancy $record) => 
                    $record->applications()
                        ->where('candidate_id', Auth::user()->candidate?->id)
                        ->where('is_offer', true)
                        ->where('status', 'offered')
                        ->exists()
                )
                ->action(function (Vacancy $record) {
                    $record->applications()
                        ->where('candidate_id', Auth::user()->candidate->id)
                        ->where('is_offer', true)
                        ->update(['status' => 'rejected']);

                    Notification::make()
                        ->title('Oferta rechazada')
                        ->danger()
                        ->send();
                }),

            Action::make('apply')
                ->label('Postularse')
                ->color('success')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->modalHeading('Confirmar Postulación')
                ->modalDescription('¿Estás seguro de que deseas postularte a esta vacante?')
                ->hidden(fn (Vacancy $record) => $record->applications()->where('candidate_id', Auth::user()->candidate?->id)->exists())
                ->action(function (Vacancy $record) {
                    $candidate = Auth::user()->candidate;

                    if (! $candidate) {
                        Notification::make()
                            ->title('Error')
                            ->body('Debes completar tu perfil antes de postularte.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $record->applications()->create([
                        'candidate_id' => $candidate->id,
                        'status' => 'pending',
                    ]);

                    Notification::make()
                        ->title('Postulación enviada')
                        ->body("Te has postulado exitosamente a {$record->title}")
                        ->success()
                        ->send();
                }),
        ];
    }
}
