<?php

use App\Models\Application;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vacancy;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;

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
