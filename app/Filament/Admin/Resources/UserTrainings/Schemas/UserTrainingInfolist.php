<?php

namespace App\Filament\Admin\Resources\UserTrainings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserTrainingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Asignación')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('user.name')
                                ->label('Usuario'),
                            TextEntry::make('training.title')
                                ->label('Capacitación'),
                            TextEntry::make('status')
                                ->label('Estado')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'pending' => 'Pendiente',
                                    'in_progress' => 'En progreso',
                                    'completed' => 'Completada',
                                    'expired' => 'Expirada',
                                    default => $state,
                                })
                                ->color(fn (string $state): string => match ($state) {
                                    'pending' => 'gray',
                                    'in_progress' => 'info',
                                    'completed' => 'success',
                                    'expired' => 'danger',
                                    default => 'gray',
                                }),
                            TextEntry::make('score')
                                ->label('Calificación')
                                ->placeholder('N/A'),
                            TextEntry::make('completed_at')
                                ->label('Completado')
                                ->dateTime('d/m/Y H:i')
                                ->placeholder('Pendiente'),
                            TextEntry::make('expires_at')
                                ->label('Expira')
                                ->dateTime('d/m/Y H:i')
                                ->placeholder('N/A'),
                        ]),
                    ]),
            ]);
    }
}
