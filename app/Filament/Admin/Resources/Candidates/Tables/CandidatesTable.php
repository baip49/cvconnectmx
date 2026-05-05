<?php

namespace App\Filament\Admin\Resources\Candidates\Tables;

use App\Models\Candidate;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CandidatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('professional_title')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('is_blocked')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? 'Bloqueado' : 'Activo')
                    ->color(fn (bool $state) => $state ? 'danger' : 'success'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('toggle_block')
                    ->label(fn (Candidate $record) => $record->is_blocked ? 'Desbloquear' : 'Bloquear')
                    ->icon(fn (Candidate $record) => $record->is_blocked ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed')
                    ->color(fn (Candidate $record) => $record->is_blocked ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->action(function (Candidate $record) {
                        $record->update(['is_blocked' => ! $record->is_blocked]);
                        Notification::make()
                            ->title($record->is_blocked ? 'Candidato bloqueado' : 'Candidato desbloqueado')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
