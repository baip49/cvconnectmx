<?php

namespace App\Filament\Admin\Resources\CandidateDocuments\Pages;

use App\Filament\Admin\Resources\CandidateDocuments\CandidateDocumentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCandidateDocument extends ViewRecord
{
    protected static string $resource = CandidateDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
