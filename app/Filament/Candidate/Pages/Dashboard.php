<?php

namespace App\Filament\Candidate\Pages;

use App\Filament\Candidate\Widgets\Candidate\ApplicationsOverview;
use App\Filament\Candidate\Widgets\Candidate\RecentApplications;
use App\Filament\Candidate\Widgets\Candidate\SuggestedVacancies;
use App\Filament\Candidate\Widgets\Candidate\WelcomeBanner;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Panel del candidato';

    protected static ?string $navigationLabel = 'Panel principal';

    public function getWidgets(): array
    {
        return [
            WelcomeBanner::class,
            ApplicationsOverview::class,
            RecentApplications::class,
            SuggestedVacancies::class,
        ];
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
