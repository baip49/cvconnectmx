<?php

namespace App\Filament\Admin\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Rol')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Rol')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('description')
                            ->label('Descripción')
                            ->maxLength(255),
                        Toggle::make('active')
                            ->label('Activo')
                            ->default(true),
                    ]),
                Section::make('Permisos Asociados')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->columns(2)
                            ->gridDirection('vertical'),
                    ]),
            ]);
    }
}
