<?php

namespace App\Filament\Admin\Resources\Trainings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TrainingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Capacitación')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('title')
                                ->label('Título'),
                            TextEntry::make('type')
                                ->label('Tipo')
                                ->badge(),
                            TextEntry::make('targetRole.name')
                                ->label('Rol Objetivo')
                                ->placeholder('Todos los roles'),
                            TextEntry::make('validity_days')
                                ->label('Validez')
                                ->suffix(' días'),
                            TextEntry::make('is_active')
                                ->label('Estado')
                                ->badge()
                                ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                                ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                            TextEntry::make('created_at')
                                ->label('Creado')
                                ->dateTime('d/m/Y H:i'),
                        ]),
                    ]),
            ]);
    }
}
