<?php

namespace App\Filament\Candidate\Resources\Vacancies\Tables;

use App\Models\Vacancy;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class VacanciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('candidate.vacancies.titulo'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('candidate.vacancies.empresa'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('work_model')
                    ->label(__('candidate.vacancies.modalidad'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'remote' => __('candidate.vacancies.remoto'),
                        'hybrid' => __('candidate.vacancies.hibrido'),
                        'on_site', 'on-site' => __('candidate.vacancies.presencial'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'remote' => 'success',
                        'hybrid' => 'warning',
                        'on_site', 'on-site' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('max_salary')
                    ->label(__('candidate.vacancies.sueldo'))
                    ->money('MXN')
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label(__('candidate.vacancies.publicada'))
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('work_model')
                    ->label(__('candidate.vacancies.modalidad'))
                    ->options([
                        'remote' => __('candidate.vacancies.remoto'),
                        'hybrid' => __('candidate.vacancies.hibrido'),
                        'on_site' => __('candidate.vacancies.presencial'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
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

                        if ($candidate->is_blocked) {
                            Notification::make()
                                ->title(__('candidate.vacancies.accion_denegada'))
                                ->body(__('candidate.vacancies.tu_cuenta_ha_sido_bloqueada_no_puedes_po'))
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
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([]);
    }
}
