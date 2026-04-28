<?php

namespace App\Filament\Candidate\Widgets\Candidate;

use App\Models\Application;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ApplicationsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $candidateId = Auth::user()->candidate?->id;

        if (! $candidateId) {
            return [];
        }

        return [
            Stat::make('Total Postulaciones', Application::where('candidate_id', $candidateId)->count())
                ->description('Tus candidaturas enviadas')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('primary'),
            Stat::make('Postulaciones Pendientes', Application::where('candidate_id', $candidateId)->where('status', 'pending')->count())
                ->description('En espera de revisión')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Entrevistas / Avances', Application::where('candidate_id', $candidateId)->whereIn('status', ['interview', 'accepted'])->count())
                ->description('Candidaturas con progreso')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
