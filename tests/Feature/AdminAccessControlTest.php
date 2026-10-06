<?php

use App\Filament\Admin\Resources\Candidates\CandidateResource;
use App\Filament\Admin\Resources\Companies\CompanyResource;
use App\Filament\Admin\Resources\Roles\RoleResource;
use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Company\Resources\Applications\ApplicationResource;
use App\Filament\Company\Resources\Vacancies\VacancyResource;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    config()->set('app.env', 'local');
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

test('candidate edition requires the manage permission', function () {
    $candidate = Candidate::factory()->create();

    expect(CandidateResource::canEdit($candidate))->toBeFalse();

    actingAs(User::factory()->admin()->create());

    expect(CandidateResource::canEdit($candidate))->toBeTrue();
});

test('company creation page is gone', function () {
    actingAs(User::factory()->admin()->create());

    get('/admin/companies/create')->assertNotFound();
});

test('admin creates users with a random password', function () {
    actingAs(User::factory()->admin()->create());

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Nuevo',
            'email' => 'nuevo@example.com',
            'role_id' => Role::query()->where('name', 'candidate')->value('id'),
        ])
        ->call('create')
        ->assertHasNoErrors();

    $first = User::query()->where('email', 'nuevo@example.com')->firstOrFail();

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Otro',
            'email' => 'otro@example.com',
            'role_id' => Role::query()->where('name', 'candidate')->value('id'),
        ])
        ->call('create')
        ->assertHasNoErrors();

    $second = User::query()->where('email', 'otro@example.com')->firstOrFail();

    expect($first->password)->not->toBeNull()
        ->and($second->password)->not->toBe($first->password)
        ->and(Hash::check('', $first->password))->toBeFalse();
});

test('roles carry their default permission sets', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);

    $codes = fn (string $role): array => Role::query()->where('name', $role)->firstOrFail()
        ->permissions()->pluck('code')->all();

    expect($codes('candidate'))->toContain('tickets.view')
        ->and($codes('candidate'))->not->toContain('vacancies.manage')
        ->and($codes('company'))->toContain('vacancies.manage', 'applications.manage')
        ->and($codes('support'))->toContain('tickets.claim', 'tickets.join')
        ->and($codes('support'))->not->toContain('tickets.assign', 'tickets.manage');
});

test('resources enforce their permissions', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);

    $candidate = User::factory()->candidate()->create();
    $company = User::factory()->company()->create();
    $admin = User::factory()->admin()->create();

    actingAs($candidate);

    expect(UserResource::canViewAny())->toBeFalse()
        ->and(UserResource::canCreate())->toBeFalse()
        ->and(VacancyResource::canCreate())->toBeFalse();

    actingAs($company);

    expect(VacancyResource::canCreate())->toBeTrue()
        ->and(ApplicationResource::canViewAny())->toBeTrue();

    actingAs($admin);

    expect(UserResource::canCreate())->toBeTrue()
        ->and(RoleResource::canEdit(Role::first()))->toBeTrue()
        ->and(CompanyResource::canEdit(Company::first()))->toBeTrue();
});

test('only the login route exists, panel logins are gone', function () {
    get('/login')->assertOk();
    get('/company/login')->assertNotFound();
    get('/support/login')->assertNotFound();
});

test('company edition requires the manage permission', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);

    $company = Company::factory()->create();

    expect(CompanyResource::canEdit($company))->toBeFalse();

    actingAs(User::factory()->admin()->create());

    expect(CompanyResource::canEdit($company))->toBeTrue();
});
