<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
