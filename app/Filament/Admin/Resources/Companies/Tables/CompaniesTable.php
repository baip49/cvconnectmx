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
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Contacto')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sector')
                    ->label('Sector')
                    ->searchable(),
                IconColumn::make('is_verified')
                    ->label('Verificada')
                    ->boolean(),
                TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Registrado')
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
