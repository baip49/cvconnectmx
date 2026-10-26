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
            Stat::make(__('support.support_stats_overview.sin_reclamar'), $unclaimed)
                ->description(__('support.support_stats_overview.tickets_esperando_asistente'))
                ->descriptionIcon('heroicon-m-ticket')
                ->color($unclaimed > 0 ? 'danger' : 'success'),

            Stat::make(__('support.support_stats_overview.en_atencion'), $inProgress)
                ->description(__('support.support_stats_overview.siendo_atendidos_ahora'))
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning'),

            Stat::make(__('support.support_stats_overview.cerrados'), $closed)
                ->description(__('support.support_stats_overview.asuntos_resueltos'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
