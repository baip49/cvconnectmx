<?php

namespace App\Filament\Admin\Resources\LoginAttempts\Pages;

use App\Filament\Admin\Resources\LoginAttempts\LoginAttemptResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLoginAttempt extends ViewRecord
{
    protected static string $resource = LoginAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
