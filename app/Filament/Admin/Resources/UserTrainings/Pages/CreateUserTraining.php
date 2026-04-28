<?php

namespace App\Filament\Admin\Resources\UserTrainings\Pages;

use App\Filament\Admin\Resources\UserTrainings\UserTrainingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserTraining extends CreateRecord
{
    protected static string $resource = UserTrainingResource::class;
}
