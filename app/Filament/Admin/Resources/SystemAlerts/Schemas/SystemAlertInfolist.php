<?php

namespace App\Filament\Admin\Resources\SystemAlerts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SystemAlertInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Alerta')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('type')
                                ->label('Tipo')
                                ->badge(),
                            TextEntry::make('level')
                                ->label('Nivel')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'info' => 'Informativo',
                                    'warning' => 'Advertencia',
                                    'error' => 'Error',
                                    'critical' => 'Crítico',
                                    default => $state,
                                })
                                ->color(fn (string $state): string => match ($state) {
                                    'info' => 'info',
                                    'warning' => 'warning',
                                    'error' => 'danger',
                                    'critical' => 'danger',
                                    default => 'gray',
                                }),
                            TextEntry::make('is_resolved')
                                ->label('Estado')
                                ->badge()
                                ->formatStateUsing(fn (bool $state): string => $state ? 'Resuelta' : 'Pendiente')
                                ->color(fn (bool $state): string => $state ? 'success' : 'warning'),
                            TextEntry::make('user.name')
                                ->label('Asignado a')
                                ->placeholder('Sin asignar'),
                            TextEntry::make('reviewedBy.name')
                                ->label('Revisado por')
                                ->placeholder('N/A'),
                            TextEntry::make('created_at')
                                ->label('Creado')
                                ->dateTime('d/m/Y H:i'),
                        ]),
                    ]),
                Section::make('Mensaje')
                    ->schema([
                        TextEntry::make('message')
                            ->label('Mensaje')
                            ->columnSpanFull(),
                    ]),
                Section::make('Resolución')
                    ->schema([
                        TextEntry::make('resolved_at')
                            ->label('Fecha de Resolución')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Pendiente'),
                        TextEntry::make('resolution_notes')
                            ->label('Notas de Resolución')
                            ->placeholder('Sin notas')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->is_resolved),
            ]);
    }
}
