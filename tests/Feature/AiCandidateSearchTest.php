<?php

use App\Filament\Company\Pages\AiCandidateSearch;
use App\Models\Skill;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

function useCompanyPanel(): void
{
    Filament::setCurrentPanel(Filament::getPanel('company'));
}

function seedSearchCandidates(): array
{
    $make = function (?int $rating, string $skill): object {
        $user = User::factory()->candidate()->create();
        $user->candidate->update(['ai_rating' => $rating]);
        Skill::create(['candidate_id' => $user->candidate->id, 'name' => $skill, 'level' => 'Avanzado']);

        return $user->candidate->refresh();
    };

    return [
        'senior' => $make(90, 'Laravel'),
        'mid' => $make(70, 'React'),
        'unrated' => $make(null, 'Laravel'),
    ];
}

function fakeOllamaSearch(): void
{
    config()->set('services.ollama.url', 'http://127.0.0.1:11434');
    config()->set('services.ollama.model', 'qwen2.5:3b');

    Http::fake([
        '127.0.0.1:11434/api/chat' => Http::response([
            'message' => ['content' => '{"skills": ["Laravel"], "keywords": ["Laravel"]}'],
        ], 200),
    ]);
}

test('company can open the AI candidate search page', function () {
    // Filament aborts with 403 outside local env when User omits FilamentUser.
    config()->set('app.env', 'local');

    actingAs(User::factory()->company()->create());

    get('/company/ai-candidate-search')->assertOk();
});

test('candidates are ordered by AI rating descending with unrated last', function () {
    config()->set('app.env', 'local');

    $candidates = seedSearchCandidates();
    actingAs(User::factory()->company()->create());
    useCompanyPanel();

    Livewire::test(AiCandidateSearch::class)
        ->assertCanSeeTableRecords([$candidates['senior'], $candidates['mid'], $candidates['unrated']], inOrder: true);
});

test('single word search matches candidate skills even without AI skills', function () {
    config()->set('app.env', 'local');
    config()->set('services.ollama.url', 'http://127.0.0.1:11434');
    config()->set('services.ollama.model', 'qwen2.5:3b');

    Http::fake([
        '127.0.0.1:11434/api/chat' => Http::response([
            'message' => ['content' => '{"skills": [], "keywords": ["filament"]}'],
        ], 200),
    ]);

    $user = User::factory()->candidate()->create();
    $user->candidate->update(['ai_rating' => 80]);
    Skill::create(['candidate_id' => $user->candidate->id, 'name' => 'Filament', 'level' => 'Avanzado']);
    $candidate = $user->candidate->refresh();

    actingAs(User::factory()->company()->create());
    useCompanyPanel();

    Livewire::test(AiCandidateSearch::class)
        ->set('searchQuery', 'filament')
        ->call('runAiSearch')
        ->assertCanSeeTableRecords([$candidate]);
});

test('AI search filters candidates and reset restores the default table', function () {
    config()->set('app.env', 'local');
    fakeOllamaSearch();

    $candidates = seedSearchCandidates();
    actingAs(User::factory()->company()->create());
    useCompanyPanel();

    Livewire::test(AiCandidateSearch::class)
        ->set('searchQuery', 'Desarrollador Laravel')
        ->call('runAiSearch')
        ->assertCanSeeTableRecords([$candidates['senior'], $candidates['unrated']])
        ->assertCanNotSeeTableRecords([$candidates['mid']])
        ->call('resetAiSearch')
        ->assertCanSeeTableRecords([$candidates['senior'], $candidates['mid'], $candidates['unrated']]);
});
