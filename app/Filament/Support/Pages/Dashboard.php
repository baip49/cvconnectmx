<?php

namespace App\Filament\Support\Pages;

use App\Filament\Support\Widgets\OpenTickets;
use App\Filament\Support\Widgets\SupportStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        return __('support.dashboard.panel_de_asistencia');
    }

    public static function getNavigationLabel(): string
    {
        return __('support.dashboard.panel_principal');
    }

    public function getWidgets(): array
    {
        return [
            SupportStatsOverview::class,
            OpenTickets::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 3,
        ];
    }
}
