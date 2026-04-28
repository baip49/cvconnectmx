<?php

namespace App\Filament\Admin\Resources\LoginAttempts\Pages;

use App\Filament\Admin\Resources\LoginAttempts\LoginAttemptResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoginAttempt extends CreateRecord
{
    protected static string $resource = LoginAttemptResource::class;
}
