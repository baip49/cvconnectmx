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
                ->label('Realizar respaldo')
                ->icon('heroicon-o-circle-stack')
                ->color('primary')
                ->form([
                    Select::make('type')
                        ->label('Tipo')
                        ->options([
                            'full' => 'Completo',
                            'incremental' => 'Incremental',
                            'differential' => 'Diferencial',
                        ])
                        ->default('full')
                        ->required(),
                    TextInput::make('retention_days')
                        ->label('Retención (días)')
                        ->numeric()
                        ->minValue(1)
                        ->default(30)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $backupLog = BackupLog::create([
                        'type' => $data['type'],
                        'frequency' => 'manual',
                        'destination_path' => 'pending',
                        'size_bytes' => 0,
                        'checksum_sha256' => '',
                        'is_encrypted' => false,
                        'status' => 'in_progress',
                        'retention_days' => (int) $data['retention_days'],
                        'executed_by' => Auth::id(),
                    ]);

                    RunDatabaseBackup::dispatch($backupLog->id);

                    Notification::make()
                        ->title('Respaldo encolado')
                        ->body('Se está generando en segundo plano; puedes abandonar esta página. Te avisaremos por Telegram al iniciar y al terminar.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
