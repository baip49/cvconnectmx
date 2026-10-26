<?php

use App\Models\Application;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vacancy;
use App\Notifications\ActivityMail;
use App\Notifications\SystemAlertMail;
use App\Services\TelegramNotifier;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
});

function bellFor(User $user): int
{
    return DatabaseNotification::query()->where('notifiable_type', User::class)->where('notifiable_id', $user->id)->count();
}

test('replying notifies the other ticket participants', function () {
    $reporter = User::factory()->candidate()->create();
    $support = User::factory()->support()->create();

    $ticket = Ticket::create([
        'title' => 'Ayuda', 'type' => 'general', 'level' => 'low', 'status' => 'open',
        'description' => 'Ayuda por favor.', 'created_by' => $reporter->id, 'detected_at' => now(),
    ]);

    $ticket->claim($support);

    expect(bellFor($reporter))->toBe(1);

    $ticket->addReply($reporter, 'Gracias.');

    expect(bellFor($support))->toBe(1);
    expect(bellFor($reporter))->toBe(1);
});

test('claiming closing and assigning notify the interested users', function () {
    $reporter = User::factory()->candidate()->create();
    $support = User::factory()->support()->create();
    $admin = User::factory()->admin()->create();

    $ticket = Ticket::create([
        'title' => 'Ayuda', 'type' => 'general', 'level' => 'low', 'status' => 'open',
        'description' => 'Ayuda por favor.', 'created_by' => $reporter->id, 'detected_at' => now(),
    ]);

    $ticket->claim($support);

    expect(bellFor($reporter))->toBe(1);

    $notification = DatabaseNotification::query()
        ->where('notifiable_type', User::class)
        ->where('notifiable_id', $reporter->id)
        ->firstOrFail();

    expect(json_encode($notification->data, JSON_UNESCAPED_SLASHES))->toContain("/dashboard/tickets/{$ticket->id}");

    $ticket->assignTo($support, $admin);

    expect(bellFor($reporter))->toBe(2);

    $ticket->close($support);

    expect(bellFor($reporter))->toBe(3);
});

test('new applications notify the company and status changes notify the candidate', function () {
    $companyUser = User::factory()->company()->create();
    $candidateUser = User::factory()->candidate()->create();
    $vacancy = Vacancy::factory()->create(['company_id' => $companyUser->company->id]);

    $application = Application::create([
        'candidate_id' => $candidateUser->candidate->id,
        'vacancy_id' => $vacancy->id,
        'status' => 'pending',
    ]);

    expect(bellFor($companyUser))->toBe(1);
    expect(bellFor($candidateUser))->toBe(0);

    $application->update(['status' => 'interview']);

    expect(bellFor($candidateUser))->toBe(1);
});

function makeHelpTicket(User $reporter): Ticket
{
    return Ticket::create([
        'title' => 'Ayuda', 'type' => 'general', 'level' => 'low', 'status' => 'open',
        'description' => 'Ayuda por favor.', 'created_by' => $reporter->id, 'detected_at' => now(),
    ]);
}

test('ticket replies and closing are emailed to the reporter in their language', function () {
    Notification::fake();

    $reporter = User::factory()->candidate()->create(['locale' => 'es']);
    $support = User::factory()->support()->create();
    $ticket = makeHelpTicket($reporter);

    $ticket->claim($support);
    $ticket->addReply($support, 'Ya lo reviso.');
    $ticket->close($support);

    Notification::assertSentTo($reporter, ActivityMail::class, 3);
    Notification::assertSentTo($reporter, ActivityMail::class, fn (ActivityMail $mail): bool => $mail->title === 'Nueva respuesta en tu ticket'
        && str_ends_with($mail->url, "/dashboard/tickets/{$ticket->id}"));
    Notification::assertNotSentTo($support, ActivityMail::class);
});

test('an assigned ticket emails the assignee and tells the reporter someone is handling it', function () {
    Notification::fake();

    $reporter = User::factory()->candidate()->create(['locale' => 'en']);
    $support = User::factory()->support()->create(['locale' => 'en']);
    $admin = User::factory()->admin()->create();

    makeHelpTicket($reporter)->assignTo($support, $admin);

    Notification::assertSentTo($support, ActivityMail::class, fn (ActivityMail $mail): bool => $mail->title === 'You have an assigned ticket');
    Notification::assertSentTo($reporter, ActivityMail::class, fn (ActivityMail $mail): bool => $mail->title === 'Your ticket is being handled');
});

test('offers email the candidate and their answer emails the company', function () {
    Notification::fake();

    $companyUser = User::factory()->company()->create();
    $candidateUser = User::factory()->candidate()->create();
    $vacancy = Vacancy::factory()->create(['company_id' => $companyUser->company->id]);

    $offer = Application::create([
        'candidate_id' => $candidateUser->candidate->id,
        'vacancy_id' => $vacancy->id,
        'status' => 'offered',
        'is_offer' => true,
    ]);

    Notification::assertSentTo($candidateUser, ActivityMail::class, 1);
    Notification::assertNotSentTo($companyUser, ActivityMail::class);

    $offer->update(['status' => 'accepted']);

    Notification::assertSentTo($companyUser, ActivityMail::class, 1);
    Notification::assertSentTo($candidateUser, ActivityMail::class, 1);
});

test('operations alerts sent to Telegram are also emailed to every admin', function () {
    Notification::fake();
    Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);
    config()->set('services.telegram.token', 'test-token');
    config()->set('services.telegram.chat_id', '123');

    $admin = User::factory()->admin()->create();
    $support = User::factory()->support()->create();

    app(TelegramNotifier::class)->send("✅ <b>Respaldo completado</b>\nTamaño: 2 MB");

    Notification::assertSentTo($admin, SystemAlertMail::class, fn (SystemAlertMail $mail, array $channels, object $notifiable): bool => $mail->toMail($notifiable)->subject === '✅ Respaldo completado');
    Notification::assertNotSentTo($support, SystemAlertMail::class);
    Http::assertSentCount(1);
});
