<?php

namespace App\Filament\Candidate\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Panel del candidato';

    protected static ?string $navigationLabel = 'Panel principal';

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 4,
        ];
    }
}
