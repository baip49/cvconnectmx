<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

test('welcome page shows the CVConnectMX branding and entry links', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('CVConnectMX')
        ->assertSee('Iniciar sesión')
        ->assertSee('Unirse ahora');
});

test('authenticated users see the dashboard entry on the welcome page', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Ir a mi panel')
        ->assertSee('href="/admin"', false)
        ->assertDontSee('Iniciar sesión');
});

test('users with an incorrect role are redirected to their own panel', function () {
    Route::middleware('role:admin')->get('/role-redirect-test', fn () => 'secret');

    $user = User::factory()->candidate()->create();

    $response = $this->actingAs($user)->get('/role-redirect-test');

    $response->assertRedirect('/dashboard');
});
