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
                    ->label('Vacante')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('candidate.user.name')
                    ->label('Candidato')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'interview' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Fecha de Postulación')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('change_status')
                    ->label('Cambiar Estado')
                    ->icon('heroicon-o-chevron-up-down')
                    ->form([
                        Select::make('status')
                            ->label('Nuevo Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'interview' => 'Entrevista',
                                'accepted' => 'Aceptado',
                                'rejected' => 'Rechazado',
                            ])
                            ->required(),
                    ])
                    ->action(function (Application $record, array $data) {
                        $record->update($data);
                        Notification::make()
                            ->title('Estado actualizado')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }
}
