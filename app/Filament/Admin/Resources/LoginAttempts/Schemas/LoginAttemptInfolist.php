<?php

namespace App\Filament\Admin\Resources\LoginAttempts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LoginAttemptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.login_attempts.detalles_del_intento'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('email')
                                ->label(__('admin.login_attempts.correo_electronico')),
                            TextEntry::make('is_successful')
                                ->label(__('admin.login_attempts.resultado'))
                                ->badge()
                                ->formatStateUsing(fn (bool $state): string => $state ? __('admin.login_attempts.exitoso') : __('admin.login_attempts.fallido'))
                                ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                            TextEntry::make('ip_address')
                                ->label(__('admin.login_attempts.direccion_ip')),
                            TextEntry::make('location')
                                ->label(__('admin.login_attempts.ubicacion'))
                                ->placeholder(__('admin.login_attempts.desconocida')),
                            TextEntry::make('user_agent')
                                ->label(__('admin.login_attempts.navegador'))
                                ->limit(80)
                                ->tooltip(fn ($record) => $record->user_agent)
                                ->placeholder('N/A'),
                            TextEntry::make('created_at')
                                ->label(__('admin.login_attempts.fecha_y_hora'))
                                ->dateTime('d/m/Y H:i:s'),
                        ]),
                    ]),
                Section::make(__('admin.login_attempts.informacion_del_fallo'))
                    ->schema([
                        TextEntry::make('failure_reason')
                            ->label(__('admin.login_attempts.razon_del_fallo'))
                            ->placeholder(__('admin.login_attempts.n_a_intento_exitoso'))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! $record->is_successful),
            ]);
    }
}
