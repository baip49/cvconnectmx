<?php

namespace App\Filament\Admin\Resources\LoginAttempts\Pages;

use App\Filament\Admin\Resources\LoginAttempts\LoginAttemptResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLoginAttempt extends EditRecord
{
    protected static string $resource = LoginAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
