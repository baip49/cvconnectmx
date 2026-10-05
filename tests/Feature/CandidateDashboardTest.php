<?php

use App\Filament\Candidate\Widgets\Candidate\WelcomeBanner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('candidate dashboard loads the welcome banner widget first', function () {
    // Filament aborts with 403 outside local env when User omits FilamentUser.
    config()->set('app.env', 'local');

    actingAs(User::factory()->candidate()->create());

    $html = get('/dashboard')->assertOk()->getContent();

    // The panel must serve its Vite theme, otherwise custom widget styling is lost.
    expect((string) $html)->toContain('filament/candidate/theme');

    // Widgets are lazy-loaded, so assert on their render order in the page.
    expect(strpos((string) $html, 'WelcomeBanner-0'))->toBeLessThan(strpos((string) $html, 'RecentApplications-2'))
        ->and(strpos((string) $html, 'WelcomeBanner-0'))->toBeLessThan(strpos((string) $html, 'SuggestedVacancies-3'));
});

test('welcome banner greets the candidate and shows the AI rating', function () {
    $user = User::factory()->candidate()->create();
    $user->candidate->update(['ai_rating' => 85]);

    actingAs($user);

    Livewire::test(WelcomeBanner::class)
        ->assertSee('¡Hola,')
        ->assertSee('85/100');
});
