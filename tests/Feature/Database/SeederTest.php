<?php

use App\Models\Agency;
use App\Models\Candidate;
use App\Models\Contractor;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user seeder creates 16 total users', function () {
    $this->seed(UserSeeder::class);

    $totalUsers = User::count();
    expect($totalUsers)->toBe(16); // 1 admin + 5 candidates + 5 agencies + 5 contractors
});

test('user seeder creates 1 admin user', function () {
    $this->seed(UserSeeder::class);

    $admin = User::where('user_type', 'admin')->first();
    expect($admin)->not->toBeNull();
    expect($admin->isAdmin())->toBeTrue();
    expect($admin->user_type->value)->toBe('admin');
});

test('user seeder creates 5 candidate users with candidate records', function () {
    $this->seed(UserSeeder::class);

    $candidates = User::where('user_type', 'candidate')->get();
    expect($candidates)->toHaveCount(5);

    $candidates->each(function ($user) {
        expect($user->candidate)->not->toBeNull();
        expect($user->candidate)->toBeInstanceOf(Candidate::class);
    });
});

test('user seeder creates 5 agency users with agency records', function () {
    $this->seed(UserSeeder::class);

    $agencies = User::where('user_type', 'agency')->get();
    expect($agencies)->toHaveCount(5);

    $agencies->each(function ($user) {
        expect($user->agency)->not->toBeNull();
        expect($user->agency)->toBeInstanceOf(Agency::class);
    });
});

test('user seeder creates 5 contractor users with contractor records', function () {
    $this->seed(UserSeeder::class);

    $contractors = User::where('user_type', 'contractor')->get();
    expect($contractors)->toHaveCount(5);

    $contractors->each(function ($user) {
        expect($user->contractor)->not->toBeNull();
        expect($user->contractor)->toBeInstanceOf(Contractor::class);
    });
});

test('admin user credentials are correct for login', function () {
    $this->seed(UserSeeder::class);

    $admin = User::where('user_type', 'admin')->first();
    expect($admin)->not->toBeNull();
    expect(\Illuminate\Support\Facades\Hash::check('123456789', $admin->password))->toBeTrue();
});
