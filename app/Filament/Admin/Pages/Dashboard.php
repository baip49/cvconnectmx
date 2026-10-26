<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\RecentAuditLogs;
use App\Filament\Admin\Widgets\RecentSystemAlerts;
use App\Filament\Admin\Widgets\RecentTickets;
use App\Filament\Admin\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        return __('admin.dashboard.panel_de_administracion');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.dashboard.panel_principal');
    }

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
            RecentTickets::class,
            RecentSystemAlerts::class,
        ];
    }
}
