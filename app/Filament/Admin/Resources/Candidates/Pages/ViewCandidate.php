<?php

namespace App\Filament\Admin\Resources\Candidates\Pages;

use App\Filament\Admin\Resources\Candidates\CandidateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCandidate extends ViewRecord
{
    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
