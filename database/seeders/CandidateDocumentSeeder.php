<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\CandidateDocument;
use Illuminate\Database\Seeder;

class CandidateDocumentSeeder extends Seeder
{
    public function run(): void
    {
        if (CandidateDocument::query()->exists()) {
            return;
        }

        Candidate::query()->each(function (Candidate $candidate): void {
            CandidateDocument::create([
                'candidate_id' => $candidate->id,
                'name' => 'Constancia de estudios',
                'file_path' => 'seed/candidate-'.$candidate->id.'/constancia-estudios.pdf',
                'slug' => 'constancia-estudios-candidate-'.$candidate->id,
            ]);
        });
    }
}
