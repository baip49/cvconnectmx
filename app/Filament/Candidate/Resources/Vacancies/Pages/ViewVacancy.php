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
