<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Ticket;
use App\Support\TicketLabels;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentTickets extends TableWidget
{
    protected int|string|array $columnSpan = ['default' => 'full', 'lg' => 2];

    protected static ?string $heading = 'Tickets recientes';

    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->with('creator')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Asunto')
                    ->limit(30),

                TextColumn::make('level')
                    ->label('Nivel')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TicketLabels::levels()[$state] ?? $state)
                    ->color(fn (string $state): string => TicketLabels::levelColors()[$state] ?? 'gray'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TicketLabels::statuses()[$state] ?? $state)
                    ->color(fn (string $state): string => TicketLabels::statusColors()[$state] ?? 'gray'),

                TextColumn::make('creator.name')
                    ->label('Reportado por')
                    ->placeholder('Sistema'),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->since(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Ticket $record): string => route('filament.admin.resources.tickets.view', ['record' => $record])),
            ]);
    }
}
