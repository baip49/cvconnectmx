<?php

namespace App\Filament\Candidate\Resources\Vacancies\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VacancyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('candidate.vacancies.detalles_de_la_vacante'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('title')
                                    ->label(__('candidate.vacancies.titulo')),
                                TextEntry::make('company.name')
                                    ->label(__('candidate.vacancies.empresa')),
                                TextEntry::make('work_model')
                                    ->label(__('candidate.vacancies.modalidad'))
                                    ->badge(),
                                TextEntry::make('max_salary')
                                    ->label(__('candidate.vacancies.sueldo_maximo'))
                                    ->money('MXN'),
                            ]),
                    ]),

                Section::make(__('candidate.vacancies.descripcion'))
                    ->schema([
                        TextEntry::make('description')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Section::make(__('candidate.vacancies.requisitos'))
                    ->schema([
                        TextEntry::make('requirements')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Section::make(__('candidate.vacancies.informacion_adicional'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('published_at')
                                    ->label(__('candidate.vacancies.publicada_el'))
                                    ->dateTime(),
                                TextEntry::make('expires_at')
                                    ->label(__('candidate.vacancies.vence_el'))
                                    ->dateTime(),
                                TextEntry::make('status')
                                    ->label(__('candidate.vacancies.estado'))
                                    ->badge(),
                            ]),
                    ]),
            ]);
    }
}
