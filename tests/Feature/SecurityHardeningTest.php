<?php

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Vacancy;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Filament aborts with 403 outside the local env.
    config()->set('app.env', 'local');
});

test('the login form posts to the throttled fortify route', function () {
    get(route('login'))->assertOk()->assertSee(route('login.store'), false);
});

test('inactive users cannot log in', function () {
    $user = User::factory()->inactive()->create();

    post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors('email');

    assertGuest();
});

test('locked users cannot log in until the lock expires', function () {
    $user = User::factory()->locked()->create();

    post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors('email');

    assertGuest();
});

test('support staff land on their own panel after logging in', function () {
    $user = User::factory()->support()->create();

    post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect('/support');
});

test('user audit entries never store password hashes or tokens', function () {
    $user = User::factory()->create();

    $user->update(['password' => 'otra-clave-segura']);

    $log = AuditLog::query()->where('entity_type', User::class)->where('action', 'updated')->latest('id')->firstOrFail();

    expect($log->old_data)->not->toHaveKeys(['password', 'remember_token'])
        ->and($log->new_data)->not->toHaveKey('password');
});

test('support staff cannot open the audit log', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);

    actingAs(User::factory()->support()->create());

    get('/admin/audit-logs')->assertForbidden();
});

test('candidates only see published vacancies', function () {
    actingAs(User::factory()->candidate()->create());

    get('/dashboard/vacancies/'.Vacancy::factory()->create(['status' => 'draft'])->id)->assertNotFound();
    get('/dashboard/vacancies/'.Vacancy::factory()->create()->id)->assertOk();
});

test('a candidate cannot apply twice to the same vacancy', function () {
    $application = Application::factory()->create();

    expect(fn () => Application::factory()->create([
        'candidate_id' => $application->candidate_id,
        'vacancy_id' => $application->vacancy_id,
    ]))->toThrow(UniqueConstraintViolationException::class);
});

test('unverified users are sent to verify their email before entering a panel', function () {
    actingAs(User::factory()->candidate()->unverified()->create());

    get('/dashboard')->assertRedirect(route('verification.notice'));
});

test('companies land on their own panel after verifying their email', function () {
    $user = User::factory()->company()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    actingAs($user)->get($verificationUrl)->assertRedirect('/company?verified=1');
});
