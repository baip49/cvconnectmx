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

    protected function getTableHeading(): ?string
    {
        return __('support.open_tickets.tickets_por_atender');
    }

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
                    ->label(__('support.open_tickets.asunto'))
                    ->limit(35),

                TextColumn::make('level')
                    ->label(__('support.open_tickets.nivel'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TicketLabels::levels()[$state] ?? $state)
                    ->color(fn (string $state): string => TicketLabels::levelColors()[$state] ?? 'gray'),

                TextColumn::make('creator.name')
                    ->label(__('support.open_tickets.reportado_por'))
                    ->placeholder(__('support.open_tickets.sistema')),

                TextColumn::make('claimedBy.name')
                    ->label(__('support.open_tickets.atendido_por'))
                    ->placeholder(__('support.open_tickets.sin_reclamar')),

                TextColumn::make('created_at')
                    ->label(__('support.open_tickets.fecha'))
                    ->dateTime('d/m/Y H:i')
                    ->since(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Ticket $record): string => route('filament.support.resources.tickets.view', ['record' => $record])),
            ]);
    }
}
