<?php

use App\Filament\Admin\Resources\BackupSchedules\Pages\ListSchedules;
use App\Jobs\RunDatabaseBackup;
use App\Models\BackupLog;
use App\Models\BackupSchedule;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

function useBackupAdminPanel(): void
{
    // Filament aborts with 403 outside local env when User omits FilamentUser.
    config()->set('app.env', 'local');
    Filament::setCurrentPanel(Filament::getPanel('admin'));
}

function makeSchedule(array $overrides = []): BackupSchedule
{
    return BackupSchedule::create(array_merge([
        'name' => 'Respaldo de prueba',
        'scope' => 'database',
        'frequency' => 'daily',
        'time' => '00:00',
        'retention_days' => 30,
        'is_active' => true,
        'last_run_at' => null,
    ], $overrides));
}

test('due schedule enqueues its backups', function () {
    useBackupAdminPanel();
    actingAs(User::factory()->admin()->create());
    Queue::fake();

    makeSchedule();

    $this->artisan('backup:run-scheduled')->assertSuccessful();

    expect(BackupLog::query()->where('status', 'in_progress')->count())->toBe(1);
    Queue::assertPushed(RunDatabaseBackup::class);
});

test('schedule with both scopes enqueues two backups', function () {
    useBackupAdminPanel();
    actingAs(User::factory()->admin()->create());
    Queue::fake();

    makeSchedule(['scope' => 'both']);

    $this->artisan('backup:run-scheduled')->assertSuccessful();

    expect(BackupLog::query()->where('status', 'in_progress')->count())->toBe(2);
    Queue::assertPushed(RunDatabaseBackup::class, 2);
});

test('recently run schedule is skipped', function () {
    useBackupAdminPanel();
    actingAs(User::factory()->admin()->create());
    Queue::fake();

    makeSchedule(['last_run_at' => now()]);

    $this->artisan('backup:run-scheduled')->assertSuccessful();

    expect(BackupLog::query()->count())->toBe(0);
    Queue::assertNotPushed(RunDatabaseBackup::class);
});

test('inactive schedule is skipped', function () {
    useBackupAdminPanel();
    actingAs(User::factory()->admin()->create());
    Queue::fake();

    makeSchedule(['is_active' => false]);

    $this->artisan('backup:run-scheduled')->assertSuccessful();

    expect(BackupLog::query()->count())->toBe(0);
    Queue::assertNotPushed(RunDatabaseBackup::class);
});

test('company can open the backup schedules page', function () {
    useBackupAdminPanel();
    actingAs(User::factory()->admin()->create());

    Livewire::test(ListSchedules::class)
        ->assertOk();
});
