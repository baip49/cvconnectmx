<?php

namespace App\Filament\Candidate\Widgets\Candidate;

use App\Models\Application;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ApplicationsOverview extends StatsOverviewWidget
{
    protected int | array | null $columns = 4;

    protected function getStats(): array
    {
        $candidate = Auth::user()->candidate;
        $candidateId = $candidate?->id;

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
            Stat::make('Rating IA Actual', ($candidate->ai_rating ?? 0) . '/100')
                ->description('Calificación de tu perfil')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color($candidate->ai_rating >= 80 ? 'success' : ($candidate->ai_rating >= 50 ? 'warning' : 'danger')),
        ];
    }
}
