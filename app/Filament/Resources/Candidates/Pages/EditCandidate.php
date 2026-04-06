<?php

namespace App\Filament\Resources\Candidates\Pages;

use App\Filament\Resources\Candidates\CandidateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCandidate extends EditRecord
{
    protected static string $resource = CandidateResource::class;

    /**
     * Cargar datos del usuario en el formulario
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->user) {
            $data['user'] = [
                'name' => $this->record->user->name,
                'last_name' => $this->record->user->last_name,
                'email' => $this->record->user->email,
            ];
        }

        return $data;
    }

    /**
     * Separar datos de User y Candidate antes de guardar
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Datos del usuario
        $userData = [
            'name' => $data['user']['name'] ?? null,
            'last_name' => $data['user']['last_name'] ?? null,
            'email' => $data['user']['email'] ?? null,
        ];

        // Remover nested user data del formulario
        unset($data['user']);

        // Actualizar el usuario relacionado
        if ($this->record->user) {
            $this->record->user->update($userData);
        }

        // Retornar solo datos de Candidate
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
