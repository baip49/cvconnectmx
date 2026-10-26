<?php

namespace App\Filament\Company\Resources\Applications\Tables;

use App\Models\Application;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vacancy.title')
                    ->label(__('company.applications.vacante'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('candidate.user.name')
                    ->label(__('company.applications.candidato'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('company.applications.estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('company.applications.pendiente'),
                        'interview' => __('company.applications.entrevista'),
                        'accepted' => __('company.applications.aceptado'),
                        'rejected' => __('company.applications.rechazado'),
                        'offered' => __('company.applications.ofertado'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'interview' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'offered' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label(__('company.applications.fecha_de_postulacion'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('change_status')
                    ->label(__('company.applications.cambiar_estado'))
                    ->icon('heroicon-o-chevron-up-down')
                    ->form([
                        Select::make('status')
                            ->label(__('company.applications.nuevo_estado'))
                            ->options([
                                'pending' => __('company.applications.pendiente'),
                                'interview' => __('company.applications.entrevista'),
                                'accepted' => __('company.applications.aceptado'),
                                'rejected' => __('company.applications.rechazado'),
                                'offered' => __('company.applications.ofertado'),
                            ])
                            ->required(),
                    ])
                    ->action(function (Application $record, array $data) {
                        $record->update($data);
                        Notification::make()
                            ->title(__('company.applications.estado_actualizado'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }
}
