<?php

use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Models\User;
use App\Models\Vacancy;
use App\Notifications\ActivityMail;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function (): void {
    config()->set('app.env', 'local');
});

test('a user can request verification once and the admins are notified', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $user = User::factory()->candidate()->create();

    $user->requestVerification();
    $user->requestVerification();

    expect($user->refresh()->hasPendingVerificationRequest())->toBeTrue()
        ->and($user->isVerifiedAccount())->toBeFalse();

    Notification::assertSentTo($admin, ActivityMail::class, 1);
});

test('the request can be made from the account page', function () {
    Notification::fake();

    $user = User::factory()->candidate()->create();
    actingAs($user);

    Livewire::test('pages::settings.profile')
        ->assertSee(__('shared.verification.request'))
        ->call('requestVerification')
        ->assertSee(__('shared.verification.requested'));

    expect($user->refresh()->hasPendingVerificationRequest())->toBeTrue();
});

test('only an admin can grant or remove the verified check', function () {
    Notification::fake();

    $user = User::factory()->candidate()->create();
    $support = User::factory()->support()->create();

    expect(fn () => $user->grantVerification($support))->toThrow(HttpException::class);
    expect($user->refresh()->isVerifiedAccount())->toBeFalse();

    $user->requestVerification();
    $user->grantVerification(User::factory()->admin()->create());

    expect($user->refresh()->isVerifiedAccount())->toBeTrue()
        ->and($user->hasPendingVerificationRequest())->toBeFalse();

    Notification::assertSentTo($user, ActivityMail::class);

    $user->revokeVerification(User::factory()->admin()->create());

    expect($user->refresh()->isVerifiedAccount())->toBeFalse();
});

test('admins grant the check from the users table', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    $user = User::factory()->candidate()->create();
    actingAs(User::factory()->admin()->create());

    Livewire::test(ListUsers::class)->callTableAction('verify', $user);

    expect($user->refresh()->isVerifiedAccount())->toBeTrue();
});

test('a verified company shows the badge next to its name for candidates', function () {
    $companyUser = User::factory()->company()->create(['verified_at' => now()]);
    Vacancy::factory()->create(['company_id' => $companyUser->company->id]);

    expect($companyUser->company->isVerifiedAccount())->toBeTrue();

    actingAs(User::factory()->candidate()->create());

    get('/dashboard/vacancies')->assertOk()->assertSee(__('shared.verification.badge'));
});

test('unverified accounts show no badge', function () {
    $companyUser = User::factory()->company()->create();
    Vacancy::factory()->create(['company_id' => $companyUser->company->id]);

    actingAs(User::factory()->candidate()->create());

    get('/dashboard/vacancies')->assertOk()->assertDontSee(__('shared.verification.badge'));
});
