<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.users.informacion_del_usuario'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('admin.users.nombre'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label(__('admin.users.apellido'))
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('admin.users.correo_electronico'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label(__('admin.users.contrasena'))
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(false)
                            ->helperText(__('admin.users.si_se_deja_vacio_se_genera_una_contrasen')),
                        Select::make('role_id')
                            ->label(__('admin.users.rol'))
                            ->relationship('role', 'name')
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
