<?php

namespace App\Filament\Admin\Resources\Companies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.companies.empresa'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label(__('admin.companies.contacto'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sector')
                    ->label(__('admin.companies.sector'))
                    ->searchable(),
                IconColumn::make('is_verified')
                    ->label(__('admin.companies.verificada'))
                    ->boolean(),
                TextColumn::make('city')
                    ->label(__('admin.companies.ciudad'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('admin.companies.registrado'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
