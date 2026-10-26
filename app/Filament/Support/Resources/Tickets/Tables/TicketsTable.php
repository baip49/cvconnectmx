<?php

namespace App\Filament\Support\Resources\Tickets\Tables;

use App\Support\TicketLabels;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('support.tickets.asunto'))
                    ->searchable()
                    ->limit(40)
                    ->sortable(),

                TextColumn::make('level')
                    ->label(__('support.tickets.nivel'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TicketLabels::levels()[$state] ?? $state)
                    ->color(fn (string $state): string => TicketLabels::levelColors()[$state] ?? 'gray'),

                TextColumn::make('status')
                    ->label(__('support.tickets.estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TicketLabels::statuses()[$state] ?? $state)
                    ->color(fn (string $state): string => TicketLabels::statusColors()[$state] ?? 'gray')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label(__('support.tickets.reportado_por'))
                    ->searchable()
                    ->placeholder(__('support.tickets.sistema')),

                TextColumn::make('claimedBy.name')
                    ->label(__('support.tickets.atendido_por'))
                    ->searchable()
                    ->placeholder(__('support.tickets.sin_reclamar')),

                TextColumn::make('created_at')
                    ->label(__('support.tickets.fecha'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('support.tickets.estado'))
                    ->options(TicketLabels::statuses()),
                SelectFilter::make('level')
                    ->label(__('support.tickets.nivel'))
                    ->options(TicketLabels::levels()),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
