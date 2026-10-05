<?php

namespace App\Filament\Support\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SupportStatsOverview extends StatsOverviewWidget
{
    protected int|array|null $columns = [
        'default' => 1,
        'sm' => 2,
        'lg' => 3,
    ];

    protected function getStats(): array
    {
        $unclaimed = Ticket::where('status', 'open')->whereNull('claimed_by')->count();
        $inProgress = Ticket::where('status', 'in_progress')->count();
        $closed = Ticket::where('status', 'closed')->count();

        return [
            Stat::make('Sin reclamar', $unclaimed)
                ->description('Tickets esperando asistente')
                ->descriptionIcon('heroicon-m-ticket')
                ->color($unclaimed > 0 ? 'danger' : 'success'),

            Stat::make('En atención', $inProgress)
                ->description('Siendo atendidos ahora')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning'),

            Stat::make('Cerrados', $closed)
                ->description('Asuntos resueltos')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
