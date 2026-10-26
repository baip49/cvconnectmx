<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

/**
 * @return list<string>
 */
function translationKeysUsedInCode(): array
{
    $files = collect(File::allFiles(app_path()))->merge(File::allFiles(resource_path('views')));

    return $files
        ->flatMap(function ($file): array {
            preg_match_all("/__\\('((?:admin|candidate|company|support|shared|notifications)\\.[a-z0-9_.]+)'/", $file->getContents(), $matches);

            return $matches[1];
        })
        ->unique()
        ->values()
        ->all();
}

test('every translation key used in the code exists in Spanish and English', function () {
    $keys = translationKeysUsedInCode();

    expect($keys)->not->toBeEmpty();

    $missing = collect($keys)
        ->flatMap(fn (string $key): array => array_filter([
            Lang::has($key, 'es', false) ? null : "es: {$key}",
            Lang::has($key, 'en', false) ? null : "en: {$key}",
        ]))
        ->all();

    expect($missing)->toBe([]);
});

test('every English phrase used by the auth and account screens has a Spanish translation', function () {
    $phrases = collect(File::allFiles(resource_path('views')))
        ->flatMap(function ($file): array {
            preg_match_all("/__\\('([A-Z0-9][^']*)'\\)/", $file->getContents(), $matches);

            return $matches[1];
        })
        ->reject(fn (string $phrase): bool => Str::contains($phrase, '.') && ! Str::contains($phrase, ' '))
        ->unique();

    $untranslated = $phrases->reject(fn (string $phrase): bool => Lang::has($phrase, 'es', false))->values()->all();

    expect($untranslated)->toBe([]);
});

test('the language switch saves the choice and the panel follows it', function () {
    config()->set('app.env', 'local');
    $user = User::factory()->candidate()->create();

    actingAs($user)->get(route('locale.switch', 'en'))->assertRedirect();

    expect($user->refresh()->locale)->toBe('en');

    get('/dashboard')->assertOk()->assertSee('Candidate dashboard')->assertSee('Español');

    get(route('locale.switch', 'es'));

    get('/dashboard')->assertOk()->assertSee('Panel del candidato')->assertSee('English');
});

test('guests keep the chosen language in their session', function () {
    get(route('locale.switch', 'en'));

    get(route('home'))->assertOk()->assertSee('Join now');
});

test('unsupported languages are rejected', function () {
    get(route('locale.switch', 'fr'))->assertNotFound();
});

test('guests can switch language from the home and login pages', function () {
    get(route('home'))->assertOk()->assertSee(route('locale.switch', 'es'), false)->assertSee('Español');
    get(route('login'))->assertOk()->assertSee(route('locale.switch', 'es'), false);

    get(route('locale.switch', 'es'));

    get(route('login'))->assertOk()->assertSee(route('locale.switch', 'en'), false)->assertSee('English');
});

test('the language chosen before registering is saved and used for their emails', function () {
    Notification::fake();

    get(route('locale.switch', 'es'));

    post('/register', [
        'name' => 'Lucía',
        'last_name' => 'Pérez',
        'email' => 'lucia@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
        'user_type' => 'candidate',
    ]);

    $user = User::query()->where('email', 'lucia@example.com')->firstOrFail();

    expect($user->locale)->toBe('es')
        ->and($user->preferredLocale())->toBe('es');

    Notification::assertSentTo($user, VerifyEmail::class, fn ($notification, array $channels, object $notifiable): bool => $notifiable->preferredLocale() === 'es');
});
