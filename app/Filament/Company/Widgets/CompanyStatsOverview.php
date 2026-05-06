<?php

namespace App\Filament\Company\Widgets;

use App\Models\Application;
use App\Models\Vacancy;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CompanyStatsOverview extends StatsOverviewWidget
{
    protected int | array | null $columns = 4;

    protected function getStats(): array
    {
        $company = Auth::user()->company;
        
        if (!$company) {
            return [];
        }

        $companyId = $company->id;

        return [
            Stat::make('Vacantes Activas', Vacancy::where('company_id', $companyId)->where('status', 'published')->count())
                ->description('Puestos abiertos actualmente')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),
            Stat::make('Total Postulaciones', Application::whereHas('vacancy', fn($q) => $q->where('company_id', $companyId))->count())
                ->description('Candidatos interesados')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('Pendientes de Revisar', Application::whereHas('vacancy', fn($q) => $q->where('company_id', $companyId))->where('status', 'pending')->count())
                ->description('Postulaciones por procesar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Candidatos Aceptados', Application::whereHas('vacancy', fn($q) => $q->where('company_id', $companyId))->where('status', 'accepted')->count())
                ->description('Contrataciones / Éxitos')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
