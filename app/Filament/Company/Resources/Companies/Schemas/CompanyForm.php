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
                Section::make('Información de la Empresa')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre de la Empresa')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('sector')
                            ->label('Sector / Industria')
                            ->maxLength(255),
                        TextInput::make('internal_tax_id')
                            ->label('RFC / ID Fiscal')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Ubicación')
                    ->schema([
                        TextInput::make('city')
                            ->label('Ciudad')
                            ->maxLength(255),
                        TextInput::make('state')
                            ->label('Estado')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }
}
