<?php

namespace App\Filament\Candidate\Widgets\Candidate;

use App\Models\Application;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class RecentApplications extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return __('candidate.candidate.mis_postulaciones_recientes');
    }

    public function table(Table $table): Table
    {
        $candidateId = Auth::user()->candidate?->id;

        return $table
            ->query(
                Application::query()
                    ->where('candidate_id', $candidateId)
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('vacancy.title')
                    ->label(__('candidate.candidate.vacante'))
                    ->searchable(),
                TextColumn::make('vacancy.company.name')
                    ->label(__('candidate.candidate.empresa')),
                TextColumn::make('status')
                    ->label(__('candidate.candidate.estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('candidate.candidate.pendiente'),
                        'interview' => __('candidate.candidate.entrevista'),
                        'accepted' => __('candidate.candidate.aceptado'),
                        'rejected' => __('candidate.candidate.rechazado'),
                        'offered' => __('candidate.candidate.ofertado'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'interview' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'offered' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label(__('candidate.candidate.fecha'))
                    ->dateTime()
                    ->since(),
            ]);
    }
}
