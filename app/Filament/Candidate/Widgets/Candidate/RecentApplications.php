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

    protected static ?string $heading = 'Mis Postulaciones Recientes';

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
                    ->label('Vacante')
                    ->searchable(),
                TextColumn::make('vacancy.company.name')
                    ->label('Empresa'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'interview' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->since(),
            ]);
    }
}
