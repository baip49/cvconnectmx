<?php

namespace App\Filament\Company\Resources\Vacancies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VacanciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('work_model')
                    ->label('Modalidad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'remote' => 'success',
                        'hybrid' => 'warning',
                        'on_site' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'remote' => 'Remoto',
                        'hybrid' => 'Híbrido',
                        'on_site' => 'Presencial',
                        default => $state,
                    }),
                TextColumn::make('max_salary')
                    ->label('Sueldo Máx.')
                    ->money('MXN')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'closed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Publicada',
                        'draft' => 'Borrador',
                        'closed' => 'Cerrada',
                        default => $state,
                    }),
                TextColumn::make('applications_count')
                    ->label('Postulaciones')
                    ->counts('applications')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expira')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('work_model')
                    ->label('Modalidad')
                    ->options([
                        'remote' => 'Remoto',
                        'hybrid' => 'Híbrido',
                        'on_site' => 'Presencial',
                    ]),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'published' => 'Publicada',
                        'draft' => 'Borrador',
                        'closed' => 'Cerrada',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
