<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

test('can register with candidate user type', function () {
    $response = $this->post('/register', [
        'name' => 'John',
        'last_name' => 'Candidate',
        'email' => 'candidate@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
        'user_type' => 'candidate',
    ]);

    $response->assertRedirect('/dashboard');

    $user = User::query()->where('email', 'candidate@example.com')->firstOrFail();

    expect($user->role->name)->toBe('candidate')
        ->and($user->candidate)->not->toBeNull();
});

test('can register with company user type', function () {
    $response = $this->post('/register', [
        'name' => 'Agency',
        'last_name' => 'Name',
        'email' => 'agency@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
        'user_type' => 'company',
    ]);

    $response->assertRedirect('/company');

    $user = User::query()->where('email', 'agency@example.com')->firstOrFail();

    expect($user->role->name)->toBe('company')
        ->and($user->company)->not->toBeNull();
});

test('registration fails with the retired contractor user type', function () {
    $response = $this->post('/register', [
        'name' => 'John',
        'last_name' => 'Contractor',
        'email' => 'contractor@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
        'user_type' => 'contractor',
    ]);

    $response->assertSessionHasErrors('user_type');
    assertDatabaseMissing(User::class, ['email' => 'contractor@example.com']);
});

test('the register form creates the account, sends the verification email and asks to verify it', function () {
    Notification::fake();

    Livewire::test(Register::class)
        ->set('name', 'Ana')
        ->set('last_name', 'López')
        ->set('email', 'ana@example.com')
        ->set('password', 'Password@123')
        ->set('password_confirmation', 'Password@123')
        ->set('user_type', 'company')
        ->call('register')
        ->assertRedirect('/company');

    $user = User::query()->where('email', 'ana@example.com')->firstOrFail();

    Notification::assertSentTo($user, VerifyEmail::class);

    $this->get('/company')->assertRedirect(route('verification.notice'));
    $this->get(route('verification.notice'))->assertOk();
});

test('registration fails without user type', function () {
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
    ]);

    $response->assertSessionHasErrors('user_type');
    assertDatabaseMissing(User::class, ['email' => 'john@example.com']);
});

test('registration fails with invalid user type', function () {
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
        'user_type' => 'invalid_type',
    ]);

    $response->assertSessionHasErrors('user_type');
    assertDatabaseMissing(User::class, ['email' => 'john@example.com']);
});

test('registration fails when attempting to register as admin', function () {
    $response = $this->post('/register', [
        'name' => 'Hacker User',
        'email' => 'hacker@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
        'user_type' => 'admin',
    ]);

    $response->assertSessionHasErrors('user_type');
    assertDatabaseMissing(User::class, ['email' => 'hacker@example.com']);
});

test('admin user can be created only via seeder', function () {
    $admin = User::factory()
        ->admin()
        ->create([
            'email' => 'admin@example.com',
        ]);

    $this->assertTrue($admin->isAdmin());
    $this->assertEquals('admin', $admin->role->name);
});
