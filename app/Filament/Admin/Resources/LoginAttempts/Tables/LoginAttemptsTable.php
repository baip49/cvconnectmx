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
                    ->label(__('admin.login_attempts.resultado'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('email')
                    ->label(__('admin.login_attempts.correo'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ip_address')
                    ->label(__('admin.login_attempts.direccion_ip'))
                    ->searchable(),

                TextColumn::make('location')
                    ->label(__('admin.login_attempts.ubicacion'))
                    ->placeholder(__('admin.login_attempts.desconocida')),

                TextColumn::make('failure_reason')
                    ->label(__('admin.login_attempts.razon_del_fallo_2'))
                    ->placeholder('N/A')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'invalid_credentials' => __('admin.login_attempts.credenciales_invalidas'),
                        'locked_account' => __('admin.login_attempts.cuenta_bloqueada'),
                        'inactive_account' => __('admin.login_attempts.cuenta_inactiva'),
                        default => $state ?? 'N/A',
                    })
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->failure_reason),

                TextColumn::make('created_at')
                    ->label(__('admin.login_attempts.fecha'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_successful')
                    ->label(__('admin.login_attempts.resultado'))
                    ->options([
                        1 => __('admin.login_attempts.exitoso'),
                        0 => __('admin.login_attempts.fallido'),
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
