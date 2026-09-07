<?php

namespace App\Filament\Admin\Resources\LoginAttempts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LoginAttemptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_successful')
                    ->label('Resultado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ip_address')
                    ->label('Dirección IP')
                    ->searchable(),

                TextColumn::make('location')
                    ->label('Ubicación')
                    ->placeholder('Desconocida'),

                TextColumn::make('failure_reason')
                    ->label('Razón del fallo')
                    ->placeholder('N/A')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->failure_reason),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_successful')
                    ->label('Resultado')
                    ->options([
                        1 => 'Exitoso',
                        0 => 'Fallido',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
