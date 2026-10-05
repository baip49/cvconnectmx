<?php

use App\Filament\Admin\Resources\Candidates\CandidateResource;
use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Models\Candidate;
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
