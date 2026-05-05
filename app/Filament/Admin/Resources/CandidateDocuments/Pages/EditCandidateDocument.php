<?php

namespace App\Filament\Admin\Resources\CandidateDocuments\Pages;

use App\Filament\Admin\Resources\CandidateDocuments\CandidateDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCandidateDocument extends EditRecord
{
    protected static string $resource = CandidateDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
