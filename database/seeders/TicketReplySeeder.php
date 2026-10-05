<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketReplySeeder extends Seeder
{
    public const SYSTEM_MESSAGE = 'Estamos revisando tu solicitud, en breve alguien de soporte se pondrá en contacto contigo.';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (TicketReply::query()->exists()) {
            return;
        }

        $attendant = User::query()->where('email', 'soporte@unach.mx')->first()
            ?? User::query()->where('email', 'cesar@unach.mx')->first();

        foreach (Ticket::query()->get() as $ticket) {
            TicketReply::create([
                'ticket_id' => $ticket->id,
                'message' => self::SYSTEM_MESSAGE,
                'performed_by' => null,
            ]);

            if (! in_array($ticket->status, ['in_progress', 'closed'], true)) {
                continue;
            }

            TicketReply::create([
                'ticket_id' => $ticket->id,
                'message' => 'Gracias por tu reporte, ya estamos revisando tu caso.',
                'performed_by' => $attendant?->id ?? $ticket->created_by,
            ]);

            TicketReply::create([
                'ticket_id' => $ticket->id,
                'message' => 'Gracias, quedo atento a la resolución.',
                'performed_by' => $ticket->created_by,
            ]);
        }
    }
}
