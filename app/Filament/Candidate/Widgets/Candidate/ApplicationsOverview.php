<?php

namespace App\Filament\Candidate\Widgets\Candidate;

use App\Models\Application;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ApplicationsOverview extends StatsOverviewWidget
{
    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $candidate = Auth::user()->candidate;
        $candidateId = $candidate?->id;

        if (! $candidateId) {
            return [];
        }

        return [
            Stat::make(__('candidate.candidate.total_postulaciones'), Application::where('candidate_id', $candidateId)->count())
                ->description(__('candidate.candidate.tus_candidaturas_enviadas'))
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('primary'),
            Stat::make(__('candidate.candidate.postulaciones_pendientes'), Application::where('candidate_id', $candidateId)->where('status', 'pending')->count())
                ->description(__('candidate.candidate.en_espera_de_revision'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make(__('candidate.candidate.entrevistas_avances'), Application::where('candidate_id', $candidateId)->whereIn('status', ['interview', 'accepted'])->count())
                ->description(__('candidate.candidate.candidaturas_con_progreso'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make(__('candidate.candidate.rating_ia_actual'), ($candidate->ai_rating ?? 0).'/100')
                ->description(__('candidate.candidate.calificacion_de_tu_perfil'))
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color($candidate->ai_rating >= 80 ? 'success' : ($candidate->ai_rating >= 50 ? 'warning' : 'danger')),
        ];
    }
}
