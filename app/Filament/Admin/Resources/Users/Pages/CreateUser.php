<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected ?string $generatedPassword = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uuid'] ??= (string) Str::uuid();
        $data['last_name'] ??= '';

        if (! filled($data['password'] ?? null)) {
            $this->generatedPassword = Str::password(16);
            $data['password'] = $this->generatedPassword;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->generatedPassword === null) {
            return;
        }

        Notification::make()
            ->title('Usuario creado con contraseña temporal')
            ->body('Compártela con el usuario, podrá cambiarla en su perfil: '.$this->generatedPassword)
            ->success()
            ->send();
    }
}
