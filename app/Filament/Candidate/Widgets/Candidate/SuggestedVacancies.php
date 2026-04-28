<?php

namespace App\Filament\Candidate\Widgets\Candidate;

use App\Models\Vacancy;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class SuggestedVacancies extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Vacantes Sugeridas';

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
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label('Empresa'),
                TextColumn::make('work_model')
                    ->label('Modalidad')
                    ->badge(),
                TextColumn::make('max_salary')
                    ->label('Sueldo Máximo')
                    ->money('MXN')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('published_at')
                    ->label('Publicada')
                    ->dateTime()
                    ->since(),
            ]);
    }
}
