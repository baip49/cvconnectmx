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
        // Accounts created by an admin are trusted; they skip the email verification step.
        $this->record->markEmailAsVerified();

        if ($this->generatedPassword === null) {
            return;
        }

        Notification::make()
            ->title(__('admin.users.usuario_creado_con_contrasena_temporal'))
            ->body(__('admin.users.compartela_con_el_usuario', ['password' => $this->generatedPassword]))
            ->success()
            ->send();
    }
}
