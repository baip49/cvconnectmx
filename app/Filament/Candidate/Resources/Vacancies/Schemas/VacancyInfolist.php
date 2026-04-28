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
                Section::make('Detalles de la Vacante')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Título'),
                                TextEntry::make('company.name')
                                    ->label('Empresa'),
                                TextEntry::make('work_model')
                                    ->label('Modalidad')
                                    ->badge(),
                                TextEntry::make('max_salary')
                                    ->label('Sueldo Máximo')
                                    ->money('MXN'),
                            ]),
                    ]),

                Section::make('Descripción')
                    ->schema([
                        TextEntry::make('description')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Section::make('Requisitos')
                    ->schema([
                        TextEntry::make('requirements')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Section::make('Información Adicional')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('published_at')
                                    ->label('Publicada el')
                                    ->dateTime(),
                                TextEntry::make('expires_at')
                                    ->label('Vence el')
                                    ->dateTime(),
                                TextEntry::make('status')
                                    ->label('Estado')
                                    ->badge(),
                            ]),
                    ]),
            ]);
    }
}
