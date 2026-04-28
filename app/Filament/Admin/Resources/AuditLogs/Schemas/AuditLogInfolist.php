<?php

namespace App\Filament\Admin\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles del Registro')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Usuario')
                                    ->placeholder('Sistema'),
                                TextEntry::make('action')
                                    ->label('Acción')
                                    ->badge(),
                                TextEntry::make('result')
                                    ->label('Resultado')
                                    ->badge(),
                                TextEntry::make('entity_type')
                                    ->label('Tipo de Entidad'),
                                TextEntry::make('entity_id')
                                    ->label('ID de Entidad'),
                                TextEntry::make('ip_address')
                                    ->label('Dirección IP'),
                            ]),
                    ]),
                Section::make('Cambios en los Datos')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                KeyValueEntry::make('old_data')
                                    ->label('Datos Anteriores'),
                                KeyValueEntry::make('new_data')
                                    ->label('Datos Nuevos'),
                            ]),
                    ]),
            ]);
    }
}
