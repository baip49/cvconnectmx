<?php

namespace App\Filament\Admin\Resources\UserTrainings\Pages;

use App\Filament\Admin\Resources\UserTrainings\UserTrainingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUserTraining extends EditRecord
{
    protected static string $resource = UserTrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
