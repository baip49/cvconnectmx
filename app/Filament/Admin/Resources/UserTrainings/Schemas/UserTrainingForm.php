<?php

namespace App\Filament\Admin\Resources\UserTrainings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserTrainingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.user_trainings.asignacion_de_capacitacion'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('user_id')
                                ->label(__('admin.user_trainings.usuario'))
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('training_id')
                                ->label(__('admin.user_trainings.capacitacion'))
                                ->relationship('training', 'title')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('status')
                                ->label(__('admin.user_trainings.estado'))
                                ->options([
                                    'pending' => __('admin.user_trainings.pendiente'),
                                    'in_progress' => __('admin.user_trainings.en_progreso'),
                                    'completed' => __('admin.user_trainings.completada'),
                                    'expired' => __('admin.user_trainings.expirada'),
                                ])
                                ->required()
                                ->default('pending'),
                            TextInput::make('score')
                                ->label(__('admin.user_trainings.calificacion'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->nullable(),
                            DatePicker::make('completed_at')
                                ->label(__('admin.user_trainings.fecha_de_finalizacion'))
                                ->nullable(),
                            DatePicker::make('expires_at')
                                ->label(__('admin.user_trainings.fecha_de_expiracion'))
                                ->nullable(),
                        ]),
                    ]),
            ]);
    }
}
