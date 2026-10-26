<?php

namespace App\Filament\Company\Widgets;

use App\Models\Application;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RecentApplications extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): ?string
    {
        return __('company.recent_applications.postulaciones_recientes');
    }

    public function table(Table $table): Table
    {
        $companyId = Auth::user()->company?->id;

        return $table
            ->query(
                Application::query()
                    ->whereHas('vacancy', fn ($q) => $q->where('company_id', $companyId))
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('vacancy.title')
                    ->label(__('company.recent_applications.vacante'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('candidate.user.name')
                    ->label(__('company.recent_applications.candidato'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('candidate.ai_rating')
                    ->label(__('company.recent_applications.rating_ia'))
                    ->badge()
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('company.recent_applications.estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('company.recent_applications.pendiente'),
                        'interview' => __('company.recent_applications.entrevista'),
                        'accepted' => __('company.recent_applications.aceptado'),
                        'rejected' => __('company.recent_applications.rechazado'),
                        'offered' => __('company.recent_applications.ofertado'),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('company.recent_applications.fecha'))
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ]);
    }
}
