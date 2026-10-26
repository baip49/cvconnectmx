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
                Section::make(__('admin.roles.informacion_del_rol'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('admin.roles.nombre_del_rol'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('description')
                            ->label(__('admin.roles.descripcion'))
                            ->maxLength(255),
                        Toggle::make('active')
                            ->label(__('admin.roles.activo'))
                            ->default(true),
                    ]),
                Section::make(__('admin.roles.permisos_asociados'))
                    ->schema([
                        CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->columns(2)
                            ->gridDirection('vertical'),
                    ]),
            ]);
    }
}
