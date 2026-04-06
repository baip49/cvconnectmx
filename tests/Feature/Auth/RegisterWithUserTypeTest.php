<?php

use App\Enums\UserType;
use App\Models\User;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can register with candidate user type', function () {
    $response = $this->post('/register', [
        'name' => 'John Candidate',
        'email' => 'candidate@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
        'user_type' => 'candidate',
    ]);

    $response->assertRedirect();
    assertDatabaseHas(User::class, [
        'name' => 'John Candidate',
        'email' => 'candidate@example.com',
        'user_type' => 'candidate',
    ]);
});

test('can register with agency user type', function () {
    $response = $this->post('/register', [
        'name' => 'Agency Name',
        'email' => 'agency@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
        'user_type' => 'agency',
    ]);

    $response->assertRedirect();
    assertDatabaseHas(User::class, [
        'name' => 'Agency Name',
        'email' => 'agency@example.com',
        'user_type' => 'agency',
    ]);
});

test('can register with contractor user type', function () {
    $response = $this->post('/register', [
        'name' => 'John Contractor',
        'email' => 'contractor@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
        'user_type' => 'contractor',
    ]);

    $response->assertRedirect();
    assertDatabaseHas(User::class, [
        'name' => 'John Contractor',
        'email' => 'contractor@example.com',
        'user_type' => 'contractor',
    ]);
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
    $this->assertEquals('admin', $admin->user_type->value);
});
