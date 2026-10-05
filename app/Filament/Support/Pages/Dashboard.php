<?php

namespace App\Filament\Support\Pages;

use App\Filament\Support\Widgets\OpenTickets;
use App\Filament\Support\Widgets\SupportStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Panel de asistencia';

    protected static ?string $navigationLabel = 'Panel principal';

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
