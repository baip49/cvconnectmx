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

    protected function getTableHeading(): ?string
    {
        return __('admin.recent_tickets.tickets_recientes');
    }

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
                    ->label(__('admin.recent_tickets.asunto'))
                    ->limit(30),

                TextColumn::make('level')
                    ->label(__('admin.recent_tickets.nivel'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TicketLabels::levels()[$state] ?? $state)
                    ->color(fn (string $state): string => TicketLabels::levelColors()[$state] ?? 'gray'),

                TextColumn::make('status')
                    ->label(__('admin.recent_tickets.estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TicketLabels::statuses()[$state] ?? $state)
                    ->color(fn (string $state): string => TicketLabels::statusColors()[$state] ?? 'gray'),

                TextColumn::make('creator.name')
                    ->label(__('admin.recent_tickets.reportado_por'))
                    ->placeholder(__('admin.recent_tickets.sistema')),

                TextColumn::make('created_at')
                    ->label(__('admin.recent_tickets.fecha'))
                    ->dateTime('d/m/Y H:i')
                    ->since(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Ticket $record): string => route('filament.admin.resources.tickets.view', ['record' => $record])),
            ]);
    }
}
