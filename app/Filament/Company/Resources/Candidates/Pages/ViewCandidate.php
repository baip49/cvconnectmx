<?php

namespace App\Filament\Company\Resources\Candidates\Pages;

use App\Filament\Company\Resources\Candidates\CandidateResource;
use App\Models\CvAccess;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewCandidate extends ViewRecord
{
    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $candidate = $this->getRecord();

        if ($candidate && Auth::user()->company) {
            CvAccess::create([
                'company_id' => Auth::user()->company->id,
                'candidate_id' => $candidate->id,
                'accessed_at' => now(),
            ]);
        }
    }
}
