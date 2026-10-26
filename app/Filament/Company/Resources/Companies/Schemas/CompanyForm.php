<?php

namespace App\Filament\Company\Resources\Companies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('company.companies.informacion_de_la_empresa'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('company.companies.nombre_de_la_empresa'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('sector')
                            ->label('Sector / Industria')
                            ->maxLength(255),
                        TextInput::make('internal_tax_id')
                            ->label(__('company.companies.rfc_id_fiscal'))
                            ->maxLength(255),
                    ])->columns(2),

                Section::make(__('company.companies.ubicacion'))
                    ->schema([
                        TextInput::make('city')
                            ->label(__('company.companies.ciudad'))
                            ->maxLength(255),
                        TextInput::make('state')
                            ->label(__('company.companies.estado'))
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }
}
