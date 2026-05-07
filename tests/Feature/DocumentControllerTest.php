<?php

use App\Models\CandidateDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('candidate can view a document stored in s3 through the public route', function () {
    Storage::fake('s3');

    $user = User::factory()->candidate()->create();
    $documentPath = 'candidate-documents/prueba.pdf';

    Storage::disk('s3')->put($documentPath, '%PDF-1.4 fake pdf content');

    $document = CandidateDocument::create([
        'candidate_id' => $user->candidate->id,
        'name' => 'Prueba',
        'file_path' => $documentPath,
        'slug' => 'prueba-69fc31b32fb53',
    ]);

    actingAs($user);

    $response = get(route('document.show', $document->slug));

    $response->assertOk();
    $response->assertHeader('content-disposition', 'inline; filename="prueba.pdf"');
    expect($response->streamedContent())->toContain('fake pdf content');
});
