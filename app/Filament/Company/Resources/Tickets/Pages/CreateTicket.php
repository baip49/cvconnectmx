<?php

namespace App\Filament\Company\Resources\Tickets\Pages;

use App\Filament\Company\Resources\Tickets\TicketResource;
use Database\Seeders\TicketReplySeeder;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        $data['affected_user_id'] = Auth::id();
        $data['status'] = 'open';
        $data['detected_at'] = now();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->replies()->create([
            'message' => TicketReplySeeder::SYSTEM_MESSAGE,
            'performed_by' => null,
        ]);
    }
}
