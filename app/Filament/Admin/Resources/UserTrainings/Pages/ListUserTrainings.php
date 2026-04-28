<?php

namespace App\Filament\Admin\Resources\UserTrainings\Pages;

use App\Filament\Admin\Resources\UserTrainings\UserTrainingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserTrainings extends ListRecords
{
    protected static string $resource = UserTrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
