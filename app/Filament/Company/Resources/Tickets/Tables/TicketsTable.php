<?php

namespace App\Filament\Company\Resources\Tickets\Tables;

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
                    ->label(__('company.tickets.asunto'))
                    ->searchable()
                    ->limit(40)
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('company.tickets.estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TicketLabels::statuses()[$state] ?? $state)
                    ->color(fn (string $state): string => TicketLabels::statusColors()[$state] ?? 'gray')
                    ->sortable(),

                TextColumn::make('claimedBy.name')
                    ->label(__('company.tickets.atendido_por'))
                    ->placeholder(__('company.tickets.sin_asignar')),

                TextColumn::make('updated_at')
                    ->label(__('company.tickets.actualizado'))
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('company.tickets.estado'))
                    ->options(TicketLabels::statuses()),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
