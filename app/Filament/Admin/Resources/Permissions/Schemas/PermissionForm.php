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
                Section::make(__('admin.permissions.informacion_del_permiso'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('admin.permissions.nombre_del_permiso'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('description')
                            ->label(__('admin.permissions.descripcion'))
                            ->maxLength(255),
                    ]),
            ]);
    }
}
