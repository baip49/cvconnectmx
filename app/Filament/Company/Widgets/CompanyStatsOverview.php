<?php

namespace App\Filament\Company\Widgets;

use App\Models\Application;
use App\Models\Vacancy;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CompanyStatsOverview extends StatsOverviewWidget
{
    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $company = Auth::user()->company;

        if (! $company) {
            return [];
        }

        $companyId = $company->id;

        return [
            Stat::make(__('company.company_stats_overview.vacantes_activas'), Vacancy::where('company_id', $companyId)->where('status', 'published')->count())
                ->description(__('company.company_stats_overview.puestos_abiertos_actualmente'))
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),
            Stat::make(__('company.company_stats_overview.total_postulaciones'), Application::whereHas('vacancy', fn ($q) => $q->where('company_id', $companyId))->count())
                ->description(__('company.company_stats_overview.candidatos_interesados'))
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make(__('company.company_stats_overview.pendientes_de_revisar'), Application::whereHas('vacancy', fn ($q) => $q->where('company_id', $companyId))->where('status', 'pending')->count())
                ->description(__('company.company_stats_overview.postulaciones_por_procesar'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make(__('company.company_stats_overview.candidatos_aceptados'), Application::whereHas('vacancy', fn ($q) => $q->where('company_id', $companyId))->where('status', 'accepted')->count())
                ->description(__('company.company_stats_overview.contrataciones_exitos'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
