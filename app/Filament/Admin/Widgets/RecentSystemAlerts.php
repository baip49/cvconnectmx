<?php

namespace App\Filament\Admin\Widgets;

use App\Models\SystemAlert;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentSystemAlerts extends TableWidget
{
    protected int|string|array $columnSpan = ['default' => 'full', 'lg' => 2];

    protected static ?string $heading = 'Alertas pendientes';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SystemAlert::query()
                    ->with('user')
                    ->where('is_resolved', false)
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                IconColumn::make('is_resolved')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-exclamation-circle')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('level')
                    ->label('Nivel')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'info' => 'Info',
                        'warning' => 'Adv',
                        'error' => 'Error',
                        'critical' => 'Crít',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'info' => 'info',
                        'warning' => 'warning',
                        'error' => 'danger',
                        'critical' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('message')
                    ->label('Mensaje')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->message),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->since(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (SystemAlert $record): string => route('filament.admin.resources.system-alerts.view', ['record' => $record])),
            ]);
    }
}
