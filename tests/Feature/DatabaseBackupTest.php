<?php

use App\Filament\Admin\Resources\BackupLogs\Pages\ListBackupLogs;
use App\Jobs\RunDatabaseBackup;
use App\Models\BackupLog;
use App\Models\User;
use App\Services\DatabaseBackupService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

function useAdminPanel(): void
{
    // Filament aborts with 403 outside local env when User omits FilamentUser.
    config()->set('app.env', 'local');
    Filament::setCurrentPanel(Filament::getPanel('admin'));
}

function fakeTelegram(): void
{
    config()->set('services.telegram.token', 'test-token');
    config()->set('services.telegram.chat_id', '12345');

    Http::fake([
        'api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);
}

test('running a backup from the list enqueues the job without waiting', function () {
    useAdminPanel();
    actingAs(User::factory()->admin()->create());
    Queue::fake();

    Livewire::test(ListBackupLogs::class)
        ->callAction('run_backup', ['type' => 'full', 'retention_days' => 30])
        ->assertHasNoErrors();

    assertDatabaseHas('backup_logs', ['type' => 'full', 'status' => 'in_progress']);
    Queue::assertPushed(RunDatabaseBackup::class);
});

test('backup job dumps the database and notifies telegram on start and finish', function () {
    Storage::fake('local');
    fakeTelegram();

    $source = tempnam(sys_get_temp_dir(), 'cvdb').'.sqlite';
    file_put_contents($source, 'fake-database-contents');
    config()->set('database.default', 'sqlite');
    config()->set('database.connections.sqlite.database', $source);

    $backupLog = BackupLog::create([
        'type' => 'full',
        'frequency' => 'manual',
        'destination_path' => 'pending',
        'size_bytes' => 0,
        'checksum_sha256' => '',
        'is_encrypted' => false,
        'status' => 'in_progress',
        'retention_days' => 30,
        'executed_by' => null,
    ]);

    app()->call([new RunDatabaseBackup($backupLog->id), 'handle']);

    $backupLog->refresh();

    expect($backupLog->status)->toBe('success')
        ->and(Storage::disk('local')->exists($backupLog->destination_path))->toBeTrue()
        ->and($backupLog->size_bytes)->toBeGreaterThan(0)
        ->and($backupLog->checksum_sha256)->not->toBe('')
        ->and((bool) $backupLog->is_encrypted)->toBeTrue()
        ->and(Crypt::decryptString((string) Storage::disk('local')->get($backupLog->destination_path)))->toBe('fake-database-contents');

    Http::assertSentCount(2, fn ($request) => str_contains((string) $request->url(), 'api.telegram.org'));
    Http::assertSent(fn ($request) => str_contains((string) $request['text'], 'iniciado'));
    Http::assertSent(fn ($request) => str_contains((string) $request['text'], 'completado'));

    unlink($source);
});

test('backup job marks the log as failed and notifies telegram on error', function () {
    Storage::fake('local');
    fakeTelegram();

    config()->set('database.default', 'sqlite');
    config()->set('database.connections.sqlite.database', '/ruta/inexistente/db.sqlite');

    $backupLog = BackupLog::create([
        'type' => 'full',
        'frequency' => 'manual',
        'destination_path' => 'pending',
        'size_bytes' => 0,
        'checksum_sha256' => '',
        'is_encrypted' => false,
        'status' => 'in_progress',
        'retention_days' => 30,
        'executed_by' => null,
    ]);

    app()->call([new RunDatabaseBackup($backupLog->id), 'handle']);

    expect($backupLog->refresh()->status)->toBe('failed');
    Http::assertSent(fn ($request) => str_contains((string) $request['text'], 'fallido'));
});

test('mysql backup fails clearly when mysqldump is missing', function () {
    Storage::fake('local');

    config()->set('database.default', 'mysql');
    config()->set('services.backup.mysqldump_path', 'C:/no-existe/mysqldump.exe');

    try {
        expect(fn () => app(DatabaseBackupService::class)->run())
            ->toThrow(RuntimeException::class, 'mysqldump');
    } finally {
        config()->set('database.default', 'sqlite');
    }
});

test('admin can view a backup log without errors', function () {
    useAdminPanel();
    actingAs(User::factory()->admin()->create());

    $backupLog = BackupLog::create([
        'type' => 'full',
        'frequency' => 'manual',
        'destination_path' => 'backups/2026-10-05/cvconnectmx-test.sql',
        'size_bytes' => 1234,
        'checksum_sha256' => hash('sha256', 'test'),
        'is_encrypted' => true,
        'status' => 'success',
        'retention_days' => 30,
        'executed_by' => null,
    ]);

    get("/admin/backup-logs/{$backupLog->id}")
        ->assertOk()
        ->assertSee('Checksum SHA256');
});

test('deleting a backup log also deletes its file from the local disk', function () {
    Storage::fake('local');
    Storage::disk('local')->put('backups/2026-10-05/cvconnectmx-test.sql', 'contenido-cifrado');

    $backupLog = BackupLog::create([
        'type' => 'full',
        'frequency' => 'manual',
        'destination_path' => 'backups/2026-10-05/cvconnectmx-test.sql',
        'size_bytes' => 1234,
        'checksum_sha256' => hash('sha256', 'test'),
        'is_encrypted' => true,
        'status' => 'success',
        'retention_days' => 30,
        'executed_by' => null,
    ]);

    $backupLog->delete();

    expect(BackupLog::query()->whereKey($backupLog->id)->exists())->toBeFalse()
        ->and(Storage::disk('local')->exists('backups/2026-10-05/cvconnectmx-test.sql'))->toBeFalse();
});

test('deleting a pending backup log does not fail', function () {
    $backupLog = BackupLog::create([
        'type' => 'full',
        'frequency' => 'manual',
        'destination_path' => 'pending',
        'size_bytes' => 0,
        'checksum_sha256' => '',
        'is_encrypted' => false,
        'status' => 'in_progress',
        'retention_days' => 30,
        'executed_by' => null,
    ]);

    $backupLog->delete();

    expect(BackupLog::query()->whereKey($backupLog->id)->exists())->toBeFalse();
});
