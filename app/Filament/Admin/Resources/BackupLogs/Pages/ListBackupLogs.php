<?php

namespace App\Filament\Admin\Resources\BackupLogs\Pages;

use App\Filament\Admin\Resources\BackupLogs\BackupLogResource;
use App\Jobs\RunDatabaseBackup;
use App\Models\BackupLog;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListBackupLogs extends ListRecords
{
    protected static string $resource = BackupLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('run_backup')
                ->label(__('admin.backup_logs.realizar_respaldo'))
                ->icon('heroicon-o-circle-stack')
                ->color('primary')
                ->form([
                    Select::make('type')
                        ->label(__('admin.backup_logs.tipo'))
                        ->options([
                            'full' => __('admin.backup_logs.completo'),
                            'incremental' => __('admin.backup_logs.incremental'),
                            'differential' => __('admin.backup_logs.diferencial'),
                        ])
                        ->default('full')
                        ->required(),
                    Select::make('scope')
                        ->label(__('admin.backup_logs.que_respaldar'))
                        ->options([
                            'database' => __('admin.backup_logs.base_de_datos'),
                            'files' => __('admin.backup_logs.archivos'),
                            'both' => __('admin.backup_logs.base_de_datos_y_archivos'),
                        ])
                        ->default('database')
                        ->required(),
                    TextInput::make('retention_days')
                        ->label(__('admin.backup_logs.retencion_dias'))
                        ->numeric()
                        ->minValue(1)
                        ->default(30)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $scopes = $data['scope'] === 'both' ? ['database', 'files'] : [$data['scope']];

                    foreach ($scopes as $scope) {
                        $backupLog = BackupLog::create([
                            'type' => $data['type'],
                            'frequency' => 'manual',
                            'scope' => $scope,
                            'destination_path' => 'pending',
                            'size_bytes' => 0,
                            'checksum_sha256' => '',
                            'is_encrypted' => false,
                            'status' => 'in_progress',
                            'retention_days' => (int) $data['retention_days'],
                            'executed_by' => Auth::id(),
                        ]);

                        RunDatabaseBackup::dispatch($backupLog->id, $scope);
                    }

                    Notification::make()
                        ->title(__('admin.backup_logs.respaldo_encolado'))
                        ->body(__('admin.backup_logs.se_esta_generando_en_segundo_plano_puede'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
