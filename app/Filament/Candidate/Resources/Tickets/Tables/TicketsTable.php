<?php

namespace App\Filament\Candidate\Resources\Tickets\Tables;

use App\Support\TicketLabels;
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
                    ->label(__('candidate.tickets.asunto'))
                    ->searchable()
                    ->limit(40)
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('candidate.tickets.estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TicketLabels::statuses()[$state] ?? $state)
                    ->color(fn (string $state): string => TicketLabels::statusColors()[$state] ?? 'gray')
                    ->sortable(),

                TextColumn::make('claimedBy.name')
                    ->label(__('candidate.tickets.atendido_por'))
                    ->placeholder(__('candidate.tickets.sin_asignar')),

                TextColumn::make('updated_at')
                    ->label(__('candidate.tickets.actualizado'))
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('candidate.tickets.estado'))
                    ->options(TicketLabels::statuses()),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
