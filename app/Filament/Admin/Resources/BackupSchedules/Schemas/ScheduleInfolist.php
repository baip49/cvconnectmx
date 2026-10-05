<?php

namespace App\Filament\Admin\Resources\BackupSchedules\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ScheduleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Programación de Respaldo')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('name')
                                ->label('Nombre'),
                            TextEntry::make('scope')
                                ->label('Qué respalda')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => ScheduleForm::scopes()[$state] ?? $state),
                            TextEntry::make('frequency')
                                ->label('Frecuencia')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => ScheduleForm::frequencies()[$state] ?? $state),
                            TextEntry::make('time')
                                ->label('Hora')
                                ->time('H:i'),
                            TextEntry::make('retention_days')
                                ->label('Retención')
                                ->suffix(' días'),
                            IconEntry::make('is_active')
                                ->label('Activo')
                                ->boolean(),
                            TextEntry::make('last_run_at')
                                ->label('Última ejecución')
                                ->dateTime('d/m/Y H:i')
                                ->placeholder('Nunca'),
                        ]),
                    ]),
            ]);
    }
}
