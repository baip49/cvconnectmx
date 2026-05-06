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
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Postulaciones Recientes';

    public function table(Table $table): Table
    {
        $companyId = Auth::user()->company?->id;

        return $table
            ->query(
                Application::query()
                    ->whereHas('vacancy', fn($q) => $q->where('company_id', $companyId))
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('vacancy.title')
                    ->label('Vacante')
                    ->searchable(),
                Tables\Columns\TextColumn::make('candidate.user.name')
                    ->label('Candidato')
                    ->searchable(),
                Tables\Columns\TextColumn::make('candidate.ai_rating')
                    ->label('Rating IA')
                    ->badge()
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'interview' => 'Entrevista',
                        'accepted' => 'Aceptado',
                        'rejected' => 'Rechazado',
                        'offered' => 'Ofertado',
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
                    ->label('Fecha')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ]);
    }
}
