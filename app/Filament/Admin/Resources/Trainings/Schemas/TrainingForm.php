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
                Section::make(__('admin.trainings.informacion_de_la_capacitacion'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')
                                ->label(__('admin.trainings.titulo'))
                                ->required()
                                ->maxLength(255),
                            Select::make('type')
                                ->label(__('admin.trainings.tipo'))
                                ->options([
                                    'security' => __('admin.trainings.seguridad'),
                                    'compliance' => __('admin.trainings.cumplimiento'),
                                    'onboarding' => __('admin.trainings.induccion'),
                                    'technical' => __('admin.trainings.tecnica'),
                                    'soft_skills' => __('admin.trainings.habilidades_blandas'),
                                ])
                                ->required(),
                            Select::make('target_role_id')
                                ->label(__('admin.trainings.rol_objetivo'))
                                ->relationship('targetRole', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            TextInput::make('validity_days')
                                ->label(__('admin.trainings.validez_dias'))
                                ->numeric()
                                ->required()
                                ->default(365),
                            Toggle::make('is_active')
                                ->label(__('admin.trainings.activo'))
                                ->default(true),
                        ]),
                    ]),
            ]);
    }
}
