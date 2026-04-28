<?php

namespace App\Filament\Admin\Resources\Companies\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Corporativa')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nombre de la Empresa'),
                                TextEntry::make('sector')
                                    ->label('Sector'),
                                IconEntry::make('is_verified')
                                    ->label('Verificada')
                                    ->boolean(),
                                TextEntry::make('internal_tax_id')
                                    ->label('RFC / Tax ID'),
                                TextEntry::make('city')
                                    ->label('Ciudad'),
                                TextEntry::make('state')
                                    ->label('Estado / Provincia'),
                            ]),
                    ]),

                Section::make('Información de Contacto (Usuario)')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Representante'),
                                TextEntry::make('user.email')
                                    ->label('Correo Electrónico'),
                            ]),
                    ]),

                Section::make('Vacantes Publicadas')
                    ->schema([
                        TextEntry::make('vacancies')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $vacancies = $record->vacancies;

                                if ($vacancies->isEmpty()) {
                                    return 'Sin vacantes publicadas';
                                }

                                return $vacancies->map(function ($vacancy) {
                                    return "• **{$vacancy->title}** ({$vacancy->status})";
                                })->implode("\n");
                            })
                            ->markdown(),
                    ]),
            ]);
    }
}
