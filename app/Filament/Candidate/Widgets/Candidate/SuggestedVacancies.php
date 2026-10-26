<?php

namespace App\Filament\Candidate\Widgets\Candidate;

use App\Models\Vacancy;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class SuggestedVacancies extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return __('candidate.candidate.vacantes_sugeridas');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Vacancy::query()
                    ->where('status', 'published')
                    ->where('expires_at', '>', now())
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('title')
                    ->label(__('candidate.candidate.titulo'))
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__('candidate.candidate.empresa')),
                TextColumn::make('work_model')
                    ->label(__('candidate.candidate.modalidad'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'remote' => __('candidate.candidate.remoto'),
                        'hybrid' => __('candidate.candidate.hibrido'),
                        'on_site', 'on-site' => __('candidate.candidate.presencial'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'remote' => 'success',
                        'hybrid' => 'warning',
                        'on_site', 'on-site' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('max_salary')
                    ->label(__('candidate.candidate.sueldo_maximo'))
                    ->money('MXN')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('published_at')
                    ->label(__('candidate.candidate.publicada'))
                    ->dateTime()
                    ->since(),
            ]);
    }
}
