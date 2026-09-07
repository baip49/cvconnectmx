<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\RecentAuditLogs;
use App\Filament\Admin\Widgets\RecentIncidents;
use App\Filament\Admin\Widgets\RecentSystemAlerts;
use App\Filament\Admin\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Panel de administración';

    protected static ?string $navigationLabel = 'Panel principal';

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 3,
        ];
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            RecentAuditLogs::class,
            RecentIncidents::class,
            RecentSystemAlerts::class,
        ];
    }
}
