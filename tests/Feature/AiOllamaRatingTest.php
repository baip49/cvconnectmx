<?php

use App\Models\User;
use App\Services\AiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function sampleCvPdf(): string
{
    $text = 'BT /F1 18 Tf 100 700 Td (Ingeniero de Software Senior con 8 anos en Laravel y AWS) Tj ET';
    $objects = [
        '<</Type/Catalog/Pages 2 0 R>>',
        '<</Type/Pages/Kids[3 0 R]/Count 1>>',
        '<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>',
        '<< /Length '.strlen($text)." >>\nstream\n".$text."\nendstream",
        '<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>',
    ];

    $pdf = "%PDF-1.4\n";
    $offsets = [];
    foreach ($objects as $index => $body) {
        $offsets[$index + 1] = strlen($pdf);
        $pdf .= ($index + 1)." 0 obj\n".$body."\nendobj\n";
    }
    $xrefPos = strlen($pdf);
    $count = count($objects) + 1;
    $pdf .= "xref\n0 {$count}\n0000000000 65535 f \n";
    for ($i = 1; $i < $count; $i++) {
        $pdf .= sprintf('%010d 00000 n ', $offsets[$i])."\n";
    }
    $pdf .= "trailer\n<</Size {$count}/Root 1 0 R>>\nstartxref\n{$xrefPos}\n%%EOF";

    return $pdf;
}

function configureOllamaFake(): void
{
    config()->set('services.ollama.url', 'http://127.0.0.1:11434');
    config()->set('services.ollama.model', 'qwen2.5:3b');
}

test('rates a candidate CV using local ollama', function () {
    Storage::fake('local');
    configureOllamaFake();

    Http::fake([
        '127.0.0.1:11434/api/chat' => Http::response([
            'message' => ['content' => '{"rating": 85, "summary": "Perfil senior sólido en Laravel."}'],
        ], 200),
    ]);

    $user = User::factory()->candidate()->create();
    Storage::disk('local')->put('candidate-cvs/cv.pdf', sampleCvPdf());
    $user->candidate->update(['cv_url' => 'candidate-cvs/cv.pdf']);

    $result = (new AiService)->analyzeCandidate($user->candidate->refresh());

    expect($result['rating'])->toBe(85)
        ->and($result['summary'])->toContain('Laravel');
});

test('returns zero rating when the CV file is missing', function () {
    configureOllamaFake();

    $user = User::factory()->candidate()->create();
    $user->candidate->update(['cv_url' => 'candidate-cvs/inexistente.pdf']);

    $result = (new AiService)->analyzeCandidate($user->candidate->refresh());

    expect($result['rating'])->toBe(0);
});

test('extracts search skills through local ollama', function () {
    configureOllamaFake();

    Http::fake([
        '127.0.0.1:11434/api/chat' => Http::response([
            'message' => ['content' => '{"skills": ["Laravel"], "keywords": ["Fullstack"]}'],
        ], 200),
    ]);

    $result = (new AiService)->searchCandidates('Desarrollador Fullstack Laravel');

    expect($result['skills'])->toContain('Laravel');
});
