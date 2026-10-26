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
                ->label(__('candidate.vacancies.aceptar_oferta'))
                ->color('success')
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->modalHeading(__('candidate.vacancies.aceptar_oferta_de_trabajo'))
                ->modalDescription(__('candidate.vacancies.estas_seguro_de_que_deseas_aceptar_esta'))
                ->modalSubmitActionLabel(__('candidate.vacancies.si_aceptar_oferta'))
                ->visible(fn (Vacancy $record) => $record->applications()
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
                        ->title(__('candidate.vacancies.oferta_aceptada'))
                        ->body(__('candidate.vacancies.has_aceptado_la_oferta_para_title_felici', ['title' => $record->title]))
                        ->success()
                        ->send();
                }),

            Action::make('reject_offer')
                ->label(__('candidate.vacancies.rechazar_oferta'))
                ->color('danger')
                ->icon('heroicon-o-x-mark')
                ->requiresConfirmation()
                ->modalHeading(__('candidate.vacancies.rechazar_oferta'))
                ->modalDescription(__('candidate.vacancies.estas_seguro_de_que_deseas_rechazar_esta'))
                ->modalSubmitActionLabel(__('candidate.vacancies.si_rechazar'))
                ->visible(fn (Vacancy $record) => $record->applications()
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
                        ->title(__('candidate.vacancies.oferta_rechazada'))
                        ->danger()
                        ->send();
                }),

            Action::make('apply')
                ->label(__('candidate.vacancies.postularse'))
                ->color('success')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->modalHeading(__('candidate.vacancies.confirmar_postulacion'))
                ->modalDescription(__('candidate.vacancies.estas_seguro_de_que_deseas_postularte_a'))
                ->hidden(fn (Vacancy $record) => $record->applications()->where('candidate_id', Auth::user()->candidate?->id)->exists())
                ->disabled(fn () => Auth::user()->candidate?->is_blocked)
                ->action(function (Vacancy $record) {
                    $candidate = Auth::user()->candidate;

                    if (! $candidate) {
                        Notification::make()
                            ->title(__('candidate.vacancies.error'))
                            ->body(__('candidate.vacancies.debes_completar_tu_perfil_antes_de_postu'))
                            ->danger()
                            ->send();

                        return;
                    }

                    $record->applications()->firstOrCreate(
                        ['candidate_id' => $candidate->id],
                        ['status' => 'pending'],
                    );

                    Notification::make()
                        ->title(__('candidate.vacancies.postulacion_enviada'))
                        ->body(__('candidate.vacancies.te_has_postulado_exitosamente_a_title', ['title' => $record->title]))
                        ->success()
                        ->send();
                }),
        ];
    }
}
