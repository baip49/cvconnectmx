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

    protected function getTableHeading(): ?string
    {
        return __('admin.recent_audit_logs.actividad_reciente');
    }

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
                    ->label(__('admin.recent_audit_logs.fecha'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('admin.recent_audit_logs.usuario'))
                    ->placeholder(__('admin.recent_audit_logs.sistema')),

                TextColumn::make('action')
                    ->label(__('admin.recent_audit_logs.accion'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => __('admin.recent_audit_logs.creacion'),
                        'updated' => __('admin.recent_audit_logs.actualizacion'),
                        'deleted' => __('admin.recent_audit_logs.eliminacion'),
                        'restored' => __('admin.recent_audit_logs.restauracion'),
                        'force_deleted' => __('admin.recent_audit_logs.eliminacion_permanente'),
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
                    ->label(__('admin.recent_audit_logs.entidad'))
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),

                TextColumn::make('category')
                    ->label(__('admin.recent_audit_logs.categoria'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'auth' => __('admin.recent_audit_logs.autenticacion'),
                        'access' => __('admin.recent_audit_logs.acceso'),
                        'candidate' => __('admin.recent_audit_logs.candidato'),
                        'company' => __('admin.recent_audit_logs.empresa'),
                        'vacancy' => __('admin.recent_audit_logs.vacante'),
                        'application' => __('admin.recent_audit_logs.postulacion'),
                        default => __('admin.recent_audit_logs.sistema'),
                    })
                    ->color('gray'),

                TextColumn::make('severity')
                    ->label(__('admin.recent_audit_logs.severidad'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => __('admin.recent_audit_logs.baja'),
                        'medium' => __('admin.recent_audit_logs.media'),
                        'high' => __('admin.recent_audit_logs.alta'),
                        'critical' => __('admin.recent_audit_logs.critica'),
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
