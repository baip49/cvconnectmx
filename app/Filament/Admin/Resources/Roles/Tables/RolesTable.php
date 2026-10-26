<?php

namespace App\Filament\Admin\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.roles.rol'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('admin.roles.descripcion'))
                    ->searchable(),
                IconColumn::make('active')
                    ->label(__('admin.roles.activo'))
                    ->boolean(),
                TextColumn::make('permissions_count')
                    ->label(__('admin.roles.permisos'))
                    ->counts('permissions'),
                TextColumn::make('created_at')
                    ->label(__('admin.roles.creado'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
