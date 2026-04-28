<?php

namespace App\Filament\Admin\Resources\UserTrainings\Pages;

use App\Filament\Admin\Resources\UserTrainings\UserTrainingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUserTraining extends ViewRecord
{
    protected static string $resource = UserTrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
