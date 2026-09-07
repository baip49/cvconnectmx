<?php

namespace App\Filament\Admin\Resources\Incidents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IncidentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles del Incidente')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('type')
                                ->label('Tipo')
                                ->badge(),
                            TextEntry::make('level')
                                ->label('Nivel')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'low' => 'Bajo',
                                    'medium' => 'Medio',
                                    'high' => 'Alto',
                                    default => $state,
                                })
                                ->color(fn (string $state): string => match ($state) {
                                    'low' => 'success',
                                    'medium' => 'warning',
                                    'high' => 'danger',
                                    default => 'gray',
                                }),
                            TextEntry::make('status')
                                ->label('Estado')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'open' => 'Abierto',
                                    'in_progress' => 'En progreso',
                                    'resolved' => 'Resuelto',
                                    'closed' => 'Cerrado',
                                    default => $state,
                                })
                                ->color(fn (string $state): string => match ($state) {
                                    'open' => 'danger',
                                    'in_progress' => 'warning',
                                    'resolved' => 'success',
                                    'closed' => 'gray',
                                    default => 'gray',
                                }),
                            TextEntry::make('affectedUser.name')
                                ->label('Usuario Afectado')
                                ->placeholder('N/A'),
                            TextEntry::make('detected_at')
                                ->label('Detectado')
                                ->dateTime('d/m/Y H:i'),
                            TextEntry::make('created_at')
                                ->label('Creado')
                                ->dateTime('d/m/Y H:i'),
                        ]),
                    ]),
                Section::make('Descripción')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Descripción')
                            ->columnSpanFull(),
                    ]),
                Section::make('Lecciones Aprendidas')
                    ->schema([
                        TextEntry::make('lessons_learned')
                            ->label('Lecciones')
                            ->placeholder('Sin lecciones registradas')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => ! empty($record->lessons_learned)),
            ]);
    }
}
