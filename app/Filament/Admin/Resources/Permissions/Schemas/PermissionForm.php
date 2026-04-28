<?php

namespace App\Filament\Admin\Resources\Permissions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Permiso')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Permiso')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('description')
                            ->label('Descripción')
                            ->maxLength(255),
                    ]),
            ]);
    }
}
