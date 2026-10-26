<?php

namespace App\Filament\Company\Resources\Vacancies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VacancyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('company.vacancies.informacion_de_la_vacante'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('company.vacancies.titulo_de_la_vacante'))
                            ->required()
                            ->maxLength(255),
                        Select::make('work_model')
                            ->label(__('company.vacancies.modalidad'))
                            ->options([
                                'remote' => __('company.vacancies.remoto'),
                                'hybrid' => __('company.vacancies.hibrido'),
                                'on_site' => __('company.vacancies.presencial'),
                            ])
                            ->required(),
                        TextInput::make('max_salary')
                            ->label(__('company.vacancies.sueldo_maximo_mensual'))
                            ->numeric()
                            ->prefix('$'),
                        DatePicker::make('expires_at')
                            ->label(__('company.vacancies.fecha_de_expiracion'))
                            ->required(),
                        Textarea::make('description')
                            ->label(__('company.vacancies.descripcion_del_puesto'))
                            ->required()
                            ->rows(6)
                            ->columnSpanFull(),
                        Textarea::make('requirements')
                            ->label(__('company.vacancies.requisitos_del_puesto'))
                            ->required()
                            ->rows(6)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
