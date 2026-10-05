<?php

namespace App\Filament\Admin\Resources\BackupSchedules\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function scopes(): array
    {
        return [
            'database' => 'Base de datos',
            'files' => 'Archivos',
            'both' => 'Base de datos y archivos',
        ];
    }

    public static function frequencies(): array
    {
        return [
            'daily' => 'Diario',
            'weekly' => 'Semanal',
            'monthly' => 'Mensual',
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Programación de Respaldo')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nombre')
                                ->required()
                                ->maxLength(255),
                            Select::make('scope')
                                ->label('Qué respaldar')
                                ->options(self::scopes())
                                ->default('database')
                                ->required(),
                            Select::make('frequency')
                                ->label('Cada cuánto')
                                ->options(self::frequencies())
                                ->default('daily')
                                ->required(),
                            TimePicker::make('time')
                                ->label('Hora')
                                ->default('02:00')
                                ->required(),
                            TextInput::make('retention_days')
                                ->label('Retención (días)')
                                ->numeric()
                                ->minValue(1)
                                ->default(30)
                                ->required(),
                            Toggle::make('is_active')
                                ->label('Activo')
                                ->default(true),
                        ]),
                    ]),
            ]);
    }
}
