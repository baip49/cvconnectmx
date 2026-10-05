<?php

use App\Filament\Admin\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Filament\AvatarProviders\InitialsAvatarProvider;
use App\Filament\Candidate\Resources\Tickets\Pages\CreateTicket;
use App\Livewire\TicketChat;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TicketReplySeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);
});

test('all panels serve their ticket pages', function () {
    // Filament aborts with 403 outside local env when User omits FilamentUser.
    config()->set('app.env', 'local');

    actingAs(User::factory()->admin()->create());

    get('/admin/tickets')->assertOk();
});

test('support panel serves its dashboard and tickets', function () {
    config()->set('app.env', 'local');

    actingAs(User::factory()->support()->create());

    get('/support')->assertOk();
    get('/support/tickets')->assertOk();
});

test('candidate panel serves its tickets', function () {
    config()->set('app.env', 'local');

    actingAs(User::factory()->candidate()->create());

    get('/dashboard/tickets')->assertOk();
});

test('company panel serves its tickets', function () {
    config()->set('app.env', 'local');

    actingAs(User::factory()->company()->create());

    get('/company/tickets')->assertOk();
});

test('candidate views a ticket with the live chat embedded', function () {
    config()->set('app.env', 'local');

    $reporter = makeTicketReporter();
    $ticket = makeTicket(['created_by' => $reporter->id]);
    $ticket->addReply($reporter, 'Mensaje de prueba en el chat.');

    actingAs($reporter);

    get("/dashboard/tickets/{$ticket->id}")
        ->assertOk()
        ->assertSee('Ticket #'.$ticket->id, false)
        ->assertSee('Mensaje de prueba en el chat.', false);
});

function makeTicketReporter(): User
{
    return User::factory()->candidate()->create();
}

function makeTicket(array $overrides = []): Ticket
{
    return Ticket::create(array_merge([
        'title' => 'No puedo subir mi CV',
        'type' => 'soporte',
        'level' => 'medium',
        'status' => 'open',
        'description' => 'Al subir el PDF marca error.',
        'created_by' => makeTicketReporter()->id,
        'detected_at' => now(),
    ], $overrides));
}

test('support role and ticket permissions are seeded', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(PermissionSeeder::class);

    expect(Role::query()->where('name', 'support')->exists())->toBeTrue();

    foreach (['tickets.view', 'tickets.create', 'tickets.reply', 'tickets.edit', 'tickets.claim', 'tickets.join', 'tickets.assign', 'tickets.close', 'tickets.manage', 'tickets.reopen'] as $code) {
        expect(Permission::query()->where('code', $code)->exists())->toBeTrue("missing permission {$code}");
    }

    $admin = User::factory()->admin()->create();

    expect($admin->role->permissions()->where('code', 'tickets.manage')->exists())->toBeTrue();
});

test('candidate opens a ticket and support claims it', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();

    $ticket = makeTicket(['created_by' => $reporter->id]);

    expect($ticket->canBeClaimedBy($support))->toBeTrue()
        ->and($ticket->canBeClaimedBy($reporter))->toBeFalse();

    $ticket->claim($support);

    expect($ticket->refresh()->claimed_by)->toBe($support->id)
        ->and($ticket->status)->toBe('in_progress');
});

test('a second assistant can join and the same one cannot join twice', function () {
    $support = User::factory()->support()->create();
    $second = User::factory()->support()->create();
    $ticket = makeTicket();

    $ticket->claim($support);

    expect($ticket->canBeJoinedBy($second))->toBeTrue()
        ->and($ticket->canBeJoinedBy($support))->toBeFalse();

    $ticket->joinAsSecond($second);

    expect($ticket->refresh()->secondary_assistant_id)->toBe($second->id);
});

test('reporter and attendant exchange replies', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();
    $ticket = makeTicket(['created_by' => $reporter->id]);

    $ticket->claim($support);

    $reply = $ticket->addReply($support, 'Ya lo estamos revisando.');
    $ticket->addReply($reporter, 'Gracias, quedo atento.');

    expect($reply->ticket_id)->toBe($ticket->id)
        ->and($ticket->replies()->count())->toBe(3);
});

test('reporter and attendant can close the ticket', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();

    $byReporter = makeTicket(['created_by' => $reporter->id]);
    $byReporter->close($reporter);

    expect($byReporter->refresh()->status)->toBe('closed')
        ->and($byReporter->closed_by)->toBe($reporter->id);

    $bySupport = makeTicket(['created_by' => $reporter->id]);
    $bySupport->claim($support);
    $bySupport->close($support);

    expect($bySupport->refresh()->status)->toBe('closed');
});

test('claimer can release the ticket and the second assistant is promoted', function () {
    $support = User::factory()->support()->create();
    $second = User::factory()->support()->create();
    $ticket = makeTicket();

    $ticket->claim($support);
    $ticket->joinAsSecond($second);
    $ticket->release($support);

    expect($ticket->refresh()->claimed_by)->toBe($second->id)
        ->and($ticket->secondary_assistant_id)->toBeNull()
        ->and($ticket->status)->toBe('in_progress');
});

test('last attendant releasing the ticket reopens it', function () {
    $support = User::factory()->support()->create();
    $ticket = makeTicket();

    $ticket->claim($support);
    $ticket->release($support);

    expect($ticket->refresh()->claimed_by)->toBeNull()
        ->and($ticket->status)->toBe('open');
});

test('users receive the permissions of their role, except administrative ones', function () {
    $candidate = User::factory()->candidate()->create();
    $support = User::factory()->support()->create();

    foreach (['tickets.view', 'tickets.create', 'tickets.reply', 'tickets.close', 'tickets.reopen'] as $code) {
        expect($candidate->hasPermission($code))->toBeTrue($code);
    }

    expect($candidate->hasPermission('tickets.edit'))->toBeFalse();

    expect($candidate->hasPermission('tickets.claim'))->toBeFalse();
    expect($candidate->hasPermission('tickets.assign'))->toBeFalse();

    foreach (['tickets.claim', 'tickets.join', 'tickets.edit'] as $code) {
        expect($support->hasPermission($code))->toBeTrue($code);
    }

    expect($support->hasPermission('tickets.assign'))->toBeFalse();
    expect($support->hasPermission('tickets.manage'))->toBeFalse();
});

test('ticket actions are recorded in the audit log', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();
    $ticket = makeTicket(['created_by' => $reporter->id]);

    $ticket->claim($support);
    $ticket->addReply($support, 'Lo revisamos.');

    expect(AuditLog::query()->where('entity_type', Ticket::class)->where('entity_id', $ticket->id)->count())->toBeGreaterThanOrEqual(2)
        ->and(AuditLog::query()->where('entity_type', TicketReply::class)->exists())->toBeTrue();
});

test('ticket chat sends messages and shows new ones without reload', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();
    $ticket = makeTicket(['created_by' => $reporter->id]);
    $ticket->claim($support);

    actingAs($reporter);

    Livewire::test(TicketChat::class, ['ticketId' => $ticket->id])
        ->assertSee('Al subir el PDF marca error.')
        ->set('message', 'Hola, ¿alguna novedad?')
        ->call('send')
        ->assertSee('Hola, ¿alguna novedad?');

    $ticket->addReply($support, 'Ya casi está listo.');

    $ticket->replies()->create([
        'message' => TicketReplySeeder::SYSTEM_MESSAGE,
        'performed_by' => null,
    ]);

    Livewire::test(TicketChat::class, ['ticketId' => $ticket->id])
        ->assertSee('Ya casi está listo.')
        ->assertSee('favicon.svg', false);
});

test('outsiders cannot claim reply or close foreign tickets', function () {
    $reporter = makeTicketReporter();
    $other = User::factory()->candidate()->create();
    $ticket = makeTicket(['created_by' => $reporter->id]);

    expect($ticket->canBeViewedBy($other))->toBeFalse();

    expect(fn () => $ticket->claim($other))->toThrow(HttpException::class);
    expect(fn () => $ticket->addReply($other, 'Hola'))->toThrow(HttpException::class);
    expect(fn () => $ticket->close($other))->toThrow(HttpException::class);
});

test('chat shows messages from oldest to newest', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();
    $ticket = makeTicket(['created_by' => $reporter->id]);

    $ticket->claim($support);
    $ticket->addReply($reporter, 'Primer mensaje del usuario.');
    $ticket->addReply($support, 'Segundo mensaje del asistente.');

    actingAs($reporter);

    $html = Livewire::test(TicketChat::class, ['ticketId' => $ticket->id])->html();

    expect(strpos($html, 'Al subir el PDF marca error.'))->toBeLessThan(strpos($html, 'Primer mensaje del usuario.'))
        ->and(strpos($html, 'Primer mensaje del usuario.'))->toBeLessThan(strpos($html, 'Segundo mensaje del asistente.'));
});

test('attendant can edit the ticket type when the user errs', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();
    $ticket = makeTicket(['created_by' => $reporter->id, 'type' => 'general']);

    actingAs($support);

    Livewire::test(TicketChat::class, ['ticketId' => $ticket->id])
        ->call('startEdit')
        ->set('editType', 'cuenta')
        ->set('editTitle', 'No puedo entrar a mi cuenta')
        ->call('saveEdit')
        ->assertSee('No puedo entrar a mi cuenta');

    expect($ticket->refresh()->type)->toBe('cuenta');
});

test('reporter cannot edit tickets, only support can', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();
    $ticket = makeTicket(['created_by' => $reporter->id]);

    expect($ticket->canBeEditedBy($reporter))->toBeFalse()
        ->and($ticket->canBeEditedBy($support))->toBeTrue();

    actingAs($reporter);

    Livewire::test(TicketChat::class, ['ticketId' => $ticket->id])
        ->assertDontSee('Editar ticket');
});

test('creating a ticket sends an automatic system reply', function () {
    config()->set('app.env', 'local');
    Filament::setCurrentPanel(Filament::getPanel('candidate'));
    actingAs(User::factory()->candidate()->create());

    Livewire::test(CreateTicket::class)
        ->fillForm([
            'title' => 'Necesito ayuda con mi cuenta',
            'type' => 'cuenta',
            'level' => 'high',
            'description' => 'No puedo entrar.',
        ])
        ->call('create')
        ->assertHasNoErrors();

    $ticket = Ticket::query()->where('title', 'Necesito ayuda con mi cuenta')->first();

    expect($ticket)->not->toBeNull()
        ->and($ticket->type)->toBe('cuenta')
        ->and($ticket->replies()->whereNull('performed_by')->where('message', 'like', 'Estamos revisando tu solicitud%')->exists())->toBeTrue();
});

test('user avatars default to colored initials without external services', function () {
    $user = User::factory()->make(['name' => 'César Enrique']);

    $url = (new InitialsAvatarProvider)->get($user);

    expect($url)->toStartWith('data:image/svg+xml,')
        ->and(rawurldecode($url))->toContain('CE');

    expect($user->avatarColorHex())->toMatch('/^#[0-9a-f]{6}$/')
        ->and($user->avatarColorHex())->toBe($user->avatarColorHex());
});

test('chat shows the uploaded profile photo instead of initials', function () {
    Storage::fake('public');

    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();
    $support->update(['avatar_path' => UploadedFile::fake()->image('cara.jpg')->store('avatars', 'public')]);

    $ticket = makeTicket(['created_by' => $reporter->id]);
    $ticket->claim($support);
    $ticket->addReply($support, 'Te ayudo con gusto.');

    actingAs($reporter);

    Livewire::test(TicketChat::class, ['ticketId' => $ticket->id])
        ->assertSee($support->refresh()->avatar_path, false);
});

test('audit logs list newest entries first', function () {
    config()->set('app.env', 'local');
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    actingAs(User::factory()->admin()->create());

    AuditLog::create([
        'action' => 'created', 'entity_type' => 'x', 'created_at' => now()->subDay(), 'updated_at' => now()->subDay(),
    ]);
    AuditLog::create([
        'action' => 'updated', 'entity_type' => 'y', 'created_at' => now(), 'updated_at' => now(),
    ]);

    Livewire::test(ListAuditLogs::class)
        ->assertCanSeeTableRecords([
            AuditLog::query()->where('entity_type', 'y')->first(),
            AuditLog::query()->where('entity_type', 'x')->first(),
        ], inOrder: true);
});

test('abandon notices are only visible to support and admin', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();
    $admin = User::factory()->admin()->create();
    $ticket = makeTicket(['created_by' => $reporter->id]);

    $ticket->claim($support);
    $ticket->release($support);

    actingAs($reporter);

    Livewire::test(TicketChat::class, ['ticketId' => $ticket->id])
        ->assertDontSee('abandonó el ticket', false);

    actingAs($support);

    Livewire::test(TicketChat::class, ['ticketId' => $ticket->id])
        ->assertSee('abandonó el ticket', false);

    actingAs($admin);

    Livewire::test(TicketChat::class, ['ticketId' => $ticket->id])
        ->assertSee('abandonó el ticket', false);
});

test('ticket lifecycle events appear as centered timeline messages', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();
    $ticket = makeTicket(['created_by' => $reporter->id]);

    $ticket->claim($support);
    $ticket->close($support);

    actingAs($reporter);

    $html = Livewire::test(TicketChat::class, ['ticketId' => $ticket->id])->html();

    expect(strpos($html, 'Ticket creado el'))->not->toBeFalse()
        ->and(strpos($html, 'tomó el ticket'))->not->toBeFalse()
        ->and(strpos($html, 'Ticket cerrado por'))->not->toBeFalse();
});

test('reporter can reopen their ticket within 30 days of closing', function () {
    $reporter = makeTicketReporter();
    $ticket = makeTicket(['created_by' => $reporter->id]);

    $ticket->close($reporter);

    expect($ticket->canBeReopenedBy($reporter))->toBeTrue();

    $ticket->reopen($reporter);

    expect($ticket->refresh()->status)->toBe('open')
        ->and($ticket->closed_at)->toBeNull();
});

test('tickets cannot be reopened after 30 days', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();
    $ticket = makeTicket(['created_by' => $reporter->id]);

    $ticket->close($reporter);
    $ticket->update(['closed_at' => now()->subDays(31)]);

    expect($ticket->refresh()->canBeReopenedBy($reporter))->toBeFalse()
        ->and($ticket->canBeReopenedBy($support))->toBeFalse();

    expect(fn () => $ticket->reopen($reporter))->toThrow(HttpException::class);
});

test('outsiders cannot reopen foreign tickets', function () {
    $reporter = makeTicketReporter();
    $other = User::factory()->candidate()->create();
    $ticket = makeTicket(['created_by' => $reporter->id]);

    $ticket->close($reporter);

    expect($ticket->canBeReopenedBy($other))->toBeFalse();
});

test('support can change the ticket status manually', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();
    $ticket = makeTicket(['created_by' => $reporter->id]);

    expect($ticket->canBeTransitionedBy($reporter))->toBeFalse()
        ->and($ticket->canBeTransitionedBy($support))->toBeTrue();

    $ticket->transitionTo($support, 'in_progress');

    expect($ticket->refresh()->status)->toBe('in_progress');

    expect(fn () => $ticket->transitionTo($support, 'closed'))->toThrow(HttpException::class);
});

test('ticket moves to in progress when support replies', function () {
    $reporter = makeTicketReporter();
    $support = User::factory()->support()->create();
    $ticket = makeTicket(['created_by' => $reporter->id]);

    $ticket->addReply($reporter, 'Sigo con el problema.');

    expect($ticket->refresh()->status)->toBe('open');

    $ticket->addReply($support, 'Ya lo estamos revisando.');

    expect($ticket->refresh()->status)->toBe('in_progress');
});
