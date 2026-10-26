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
                    ->label(__('admin.candidates.nombre'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label(__('admin.candidates.correo'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('professional_title')
                    ->label(__('admin.candidates.titulo'))
                    ->searchable(),
                TextColumn::make('city')
                    ->label(__('admin.candidates.ciudad'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('admin.candidates.registrado'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('is_blocked')
                    ->label(__('admin.candidates.estado'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? __('admin.candidates.bloqueado') : __('admin.candidates.activo'))
                    ->color(fn (bool $state) => $state ? 'danger' : 'success'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('toggle_block')
                    ->label(fn (Candidate $record) => $record->is_blocked ? __('admin.candidates.desbloquear') : __('admin.candidates.bloquear'))
                    ->icon(fn (Candidate $record) => $record->is_blocked ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed')
                    ->color(fn (Candidate $record) => $record->is_blocked ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->action(function (Candidate $record) {
                        $record->update(['is_blocked' => ! $record->is_blocked]);
                        Notification::make()
                            ->title($record->is_blocked ? __('admin.candidates.candidato_bloqueado') : __('admin.candidates.candidato_desbloqueado'))
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
