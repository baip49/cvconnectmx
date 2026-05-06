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
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('work_model')
                    ->label('Modalidad')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'remote' => 'Remoto',
                        'hybrid' => 'Híbrido',
                        'on_site', 'on-site' => 'Presencial',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'remote' => 'success',
                        'hybrid' => 'warning',
                        'on_site', 'on-site' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('max_salary')
                    ->label('Sueldo')
                    ->money('MXN')
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Publicada')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('work_model')
                    ->label('Modalidad')
                    ->options([
                        'remote' => 'Remoto',
                        'hybrid' => 'Híbrido',
                        'on_site' => 'Presencial',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('apply')
                    ->label('Postularse')
                    ->color('success')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Postulación')
                    ->modalDescription('¿Estás seguro de que deseas postularte a esta vacante?')
                    ->hidden(fn (Vacancy $record) => $record->applications()->where('candidate_id', Auth::user()->candidate?->id)->exists())
                    ->disabled(fn () => Auth::user()->candidate?->is_blocked)
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

                        if ($candidate->is_blocked) {
                            Notification::make()
                                ->title('Acción denegada')
                                ->body('Tu cuenta ha sido bloqueada. No puedes postularte a vacantes.')
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
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([]);
    }
}
