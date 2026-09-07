<?php

namespace App\Filament\Admin\Widgets;

use App\Models\AuditLog;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentAuditLogs extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Actividad reciente';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AuditLog::query()
                    ->with('user')
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->placeholder('Sistema'),

                TextColumn::make('action')
                    ->label('Acción')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Creación',
                        'updated' => 'Actualización',
                        'deleted' => 'Eliminación',
                        'restored' => 'Restauración',
                        'force_deleted' => 'Eliminación permanente',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'restored' => 'warning',
                        'force_deleted' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('entity_type')
                    ->label('Entidad')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),

                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'auth' => 'Autenticación',
                        'access' => 'Acceso',
                        'candidate' => 'Candidato',
                        'company' => 'Empresa',
                        'vacancy' => 'Vacante',
                        'application' => 'Postulación',
                        default => 'Sistema',
                    })
                    ->color('gray'),

                TextColumn::make('severity')
                    ->label('Severidad')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'Baja',
                        'medium' => 'Media',
                        'high' => 'Alta',
                        'critical' => 'Crítica',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'critical' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (AuditLog $record): string => route('filament.admin.resources.audit-logs.view', ['record' => $record])),
            ]);
    }
}
