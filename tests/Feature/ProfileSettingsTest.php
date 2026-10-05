<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('user can update avatar and last name in profile', function () {
    Storage::fake('public');

    $user = User::factory()->candidate()->create();
    actingAs($user);

    Livewire::test('pages::settings.profile')
        ->set('last_name', 'García')
        ->set('avatar', UploadedFile::fake()->image('foto.jpg'))
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->last_name)->toBe('García')
        ->and($user->avatar_path)->not->toBeNull()
        ->and(Storage::disk('public')->exists($user->avatar_path))->toBeTrue();
});

test('user can change password from the profile section', function () {
    $user = User::factory()->candidate()->create();
    actingAs($user);

    Livewire::test('pages::settings.profile')
        ->set('current_password', 'password')
        ->set('password', 'nueva-clave-segura-123')
        ->set('password_confirmation', 'nueva-clave-segura-123')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Hash::check('nueva-clave-segura-123', $user->refresh()->password))->toBeTrue();
});

test('password change requires the current password', function () {
    actingAs(User::factory()->candidate()->create());

    Livewire::test('pages::settings.profile')
        ->set('current_password', 'incorrecta')
        ->set('password', 'nueva-clave-segura-123')
        ->set('password_confirmation', 'nueva-clave-segura-123')
        ->call('updatePassword')
        ->assertHasErrors('current_password');
});

test('candidate menu shows the profile entry', function () {
    config()->set('app.env', 'local');
    actingAs(User::factory()->candidate()->create());

    get('/dashboard')->assertOk()->assertSee('Perfil', false);
});

test('company menu shows the profile entry', function () {
    config()->set('app.env', 'local');
    actingAs(User::factory()->company()->create());

    get('/company')->assertOk()->assertSee('Perfil', false);
});

test('admin menu shows the profile entry', function () {
    config()->set('app.env', 'local');
    actingAs(User::factory()->admin()->create());

    get('/admin')->assertOk()->assertSee('Perfil', false);
});

test('support menu shows the profile entry', function () {
    config()->set('app.env', 'local');
    actingAs(User::factory()->support()->create());

    get('/support')->assertOk()->assertSee('Perfil', false);
});
