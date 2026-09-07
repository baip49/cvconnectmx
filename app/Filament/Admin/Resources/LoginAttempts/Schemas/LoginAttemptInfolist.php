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
                Section::make('Detalles del Intento')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('email')
                                ->label('Correo Electrónico'),
                            TextEntry::make('is_successful')
                                ->label('Resultado')
                                ->badge()
                                ->formatStateUsing(fn (bool $state): string => $state ? 'Exitoso' : 'Fallido')
                                ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                            TextEntry::make('ip_address')
                                ->label('Dirección IP'),
                            TextEntry::make('location')
                                ->label('Ubicación')
                                ->placeholder('Desconocida'),
                            TextEntry::make('user_agent')
                                ->label('Navegador')
                                ->limit(80)
                                ->tooltip(fn ($record) => $record->user_agent)
                                ->placeholder('N/A'),
                            TextEntry::make('created_at')
                                ->label('Fecha y Hora')
                                ->dateTime('d/m/Y H:i:s'),
                        ]),
                    ]),
                Section::make('Información del Fallo')
                    ->schema([
                        TextEntry::make('failure_reason')
                            ->label('Razón del Fallo')
                            ->placeholder('N/A - Intento exitoso')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! $record->is_successful),
            ]);
    }
}
