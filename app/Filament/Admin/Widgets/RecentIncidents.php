<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Incident;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentIncidents extends TableWidget
{
    protected int|string|array $columnSpan = ['default' => 'full', 'lg' => 2];

    protected static ?string $heading = 'Incidentes recientes';

    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Incident::query()
                    ->with('affectedUser')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('level')
                    ->label('Nivel')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Bajo',
                        'medium' => 'Medio',
                        'high' => 'Alto',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Abierto',
                        'in_progress' => 'En progreso',
                        'resolved' => 'Resuelto',
                        'closed' => 'Cerrado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->description),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->since(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Incident $record): string => route('filament.admin.resources.incidents.view', ['record' => $record])),
            ]);
    }
}
