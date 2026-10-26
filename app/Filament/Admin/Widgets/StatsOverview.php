<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\SystemAlert;
use App\Models\Ticket;
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
        $openTickets = Ticket::whereIn('status', ['open', 'in_progress'])->count();
        $unresolvedAlerts = SystemAlert::where('is_resolved', false)->count();
        $todayLogs = AuditLog::whereDate('created_at', today())->count();

        return [
            Stat::make(__('admin.stats_overview.usuarios'), $totalUsers)
                ->description(__('admin.stats_overview.totalcandidates_candidatos_totalcompanie', ['totalCandidates' => $totalCandidates, 'totalCompanies' => $totalCompanies]))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make(__('admin.stats_overview.vacantes_activas'), $activeVacancies)
                ->description(__('admin.stats_overview.publicadas_actualmente'))
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),

            Stat::make(__('admin.stats_overview.postulaciones'), $totalApplications)
                ->description(__('admin.stats_overview.pendingapplications_pendientes_accepteda', ['pendingApplications' => $pendingApplications, 'acceptedApplications' => $acceptedApplications]))
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('info'),

            Stat::make(__('admin.stats_overview.actividad_hoy'), $todayLogs)
                ->description(__('admin.stats_overview.registros_de_auditoria'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make(__('admin.stats_overview.tickets_abiertos'), $openTickets)
                ->description(__('admin.stats_overview.requieren_atencion'))
                ->descriptionIcon('heroicon-m-lifebuoy')
                ->color($openTickets > 0 ? 'danger' : 'success'),

            Stat::make(__('admin.stats_overview.alertas_pendientes'), $unresolvedAlerts)
                ->description(__('admin.stats_overview.sin_resolver'))
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color($unresolvedAlerts > 0 ? 'warning' : 'success'),
        ];
    }
}
