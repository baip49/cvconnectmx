<?php

namespace App\Filament\Support\Widgets;

use App\Models\Ticket;
use App\Support\TicketLabels;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class OpenTickets extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Tickets por atender';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->with('creator')
                    ->whereIn('status', ['open', 'in_progress'])
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Asunto')
                    ->limit(35),

                TextColumn::make('level')
                    ->label('Nivel')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TicketLabels::levels()[$state] ?? $state)
                    ->color(fn (string $state): string => TicketLabels::levelColors()[$state] ?? 'gray'),

                TextColumn::make('creator.name')
                    ->label('Reportado por')
                    ->placeholder('Sistema'),

                TextColumn::make('claimedBy.name')
                    ->label('Atendido por')
                    ->placeholder('Sin reclamar'),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->since(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Ticket $record): string => route('filament.support.resources.tickets.view', ['record' => $record])),
            ]);
    }
}
