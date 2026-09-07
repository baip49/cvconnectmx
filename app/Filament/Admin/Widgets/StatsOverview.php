<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\Incident;
use App\Models\SystemAlert;
use App\Models\User;
use App\Models\Vacancy;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected int|array|null $columns = [
        'default' => 1,
        'sm' => 2,
        'lg' => 4,
    ];

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $totalCandidates = Candidate::count();
        $totalCompanies = Company::count();
        $activeVacancies = Vacancy::where('status', 'published')->count();
        $totalApplications = Application::count();
        $pendingApplications = Application::where('status', 'pending')->count();
        $acceptedApplications = Application::where('status', 'accepted')->count();
        $openIncidents = Incident::whereIn('status', ['open', 'in_progress'])->count();
        $unresolvedAlerts = SystemAlert::where('is_resolved', false)->count();
        $todayLogs = AuditLog::whereDate('created_at', today())->count();

        return [
            Stat::make('Usuarios', $totalUsers)
                ->description("{$totalCandidates} candidatos · {$totalCompanies} empresas")
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Vacantes activas', $activeVacancies)
                ->description('Publicadas actualmente')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),

            Stat::make('Postulaciones', $totalApplications)
                ->description("{$pendingApplications} pendientes · {$acceptedApplications} aceptadas")
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('info'),

            Stat::make('Actividad hoy', $todayLogs)
                ->description('Registros de auditoría')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Incidentes abiertos', $openIncidents)
                ->description('Requieren atención')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($openIncidents > 0 ? 'danger' : 'success'),

            Stat::make('Alertas pendientes', $unresolvedAlerts)
                ->description('Sin resolver')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color($unresolvedAlerts > 0 ? 'warning' : 'success'),
        ];
    }
}
