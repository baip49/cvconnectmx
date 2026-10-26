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
                    ->label(__('company.vacancies.titulo'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('work_model')
                    ->label(__('company.vacancies.modalidad'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'remote' => 'success',
                        'hybrid' => 'warning',
                        'on_site' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'remote' => __('company.vacancies.remoto'),
                        'hybrid' => __('company.vacancies.hibrido'),
                        'on_site' => __('company.vacancies.presencial'),
                        default => $state,
                    }),
                TextColumn::make('max_salary')
                    ->label(__('company.vacancies.sueldo_max'))
                    ->money('MXN')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('company.vacancies.estado'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'closed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => __('company.vacancies.publicada'),
                        'draft' => __('company.vacancies.borrador'),
                        'closed' => __('company.vacancies.cerrada'),
                        default => $state,
                    }),
                TextColumn::make('applications_count')
                    ->label(__('company.vacancies.postulaciones'))
                    ->counts('applications')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label(__('company.vacancies.expira'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('work_model')
                    ->label(__('company.vacancies.modalidad'))
                    ->options([
                        'remote' => __('company.vacancies.remoto'),
                        'hybrid' => __('company.vacancies.hibrido'),
                        'on_site' => __('company.vacancies.presencial'),
                    ]),
                SelectFilter::make('status')
                    ->label(__('company.vacancies.estado'))
                    ->options([
                        'published' => __('company.vacancies.publicada'),
                        'draft' => __('company.vacancies.borrador'),
                        'closed' => __('company.vacancies.cerrada'),
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
