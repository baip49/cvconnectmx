<?php

namespace App\Filament\Admin\Resources\Trainings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TrainingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Capacitación')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')
                                ->label('Título')
                                ->required()
                                ->maxLength(255),
                            Select::make('type')
                                ->label('Tipo')
                                ->options([
                                    'security' => 'Seguridad',
                                    'compliance' => 'Cumplimiento',
                                    'onboarding' => 'Inducción',
                                    'technical' => 'Técnica',
                                    'soft_skills' => 'Habilidades Blandas',
                                ])
                                ->required(),
                            Select::make('target_role_id')
                                ->label('Rol Objetivo')
                                ->relationship('targetRole', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            TextInput::make('validity_days')
                                ->label('Validez (días)')
                                ->numeric()
                                ->required()
                                ->default(365),
                            Toggle::make('is_active')
                                ->label('Activo')
                                ->default(true),
                        ]),
                    ]),
            ]);
    }
}
