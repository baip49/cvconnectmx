<?php

namespace App\Filament\Admin\Resources\Candidates\Pages;

use App\Filament\Admin\Resources\Candidates\CandidateResource;
use Filament\Resources\Pages\ListRecords;

class ListCandidates extends ListRecords
{
    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
