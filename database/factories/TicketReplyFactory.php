<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Database\Factories\Concerns\HasSpanishContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketReply>
 */
class TicketReplyFactory extends Factory
{
    use HasSpanishContent;

    protected $model = TicketReply::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'message' => $this->spanish(self::$spanishActionTexts),
            'performed_by' => User::factory(),
        ];
    }
}
