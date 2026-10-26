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
                Section::make(__('admin.companies.informacion_corporativa'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('admin.companies.nombre_de_la_empresa')),
                                TextEntry::make('sector')
                                    ->label(__('admin.companies.sector')),
                                IconEntry::make('is_verified')
                                    ->label(__('admin.companies.verificada'))
                                    ->boolean(),
                                TextEntry::make('internal_tax_id')
                                    ->label(__('admin.companies.rfc_tax_id')),
                                TextEntry::make('city')
                                    ->label(__('admin.companies.ciudad')),
                                TextEntry::make('state')
                                    ->label(__('admin.companies.estado_provincia')),
                            ]),
                    ]),

                Section::make(__('admin.companies.informacion_de_contacto_usuario'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label(__('admin.companies.representante')),
                                TextEntry::make('user.email')
                                    ->label(__('admin.companies.correo_electronico')),
                            ]),
                    ]),

                Section::make(__('admin.companies.vacantes_publicadas'))
                    ->schema([
                        TextEntry::make('vacancies')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $vacancies = $record->vacancies;

                                if ($vacancies->isEmpty()) {
                                    return __('admin.companies.sin_vacantes_publicadas');
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
