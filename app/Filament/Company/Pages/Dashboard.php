<?php

namespace App\Filament\Company\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        return __('company.dashboard.panel_de_la_empresa');
    }

    public static function getNavigationLabel(): string
    {
        return __('company.dashboard.panel_principal');
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 4,
        ];
    }
}
