<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Ticket::query()->exists()) {
            return;
        }

        $diana = User::query()->where('email', 'diana@unach.mx')->first();
        $lucas = User::query()->where('email', 'lucas@unach.mx')->first();
        $cesar = User::query()->where('email', 'cesar@unach.mx')->first();
        $soporte = User::query()->where('email', 'soporte@unach.mx')->first();

        $attendantId = $soporte?->id ?? $cesar?->id;

        if ($diana) {
            Ticket::create([
                'title' => 'No puedo subir mi CV a la plataforma',
                'type' => 'cv',
                'level' => 'high',
                'status' => 'open',
                'description' => 'Hola, intento subir mi CV en PDF desde la sección Mi CV y la página marca un error al finalizar la carga.',
                'created_by' => $diana->id,
                'affected_user_id' => $diana->id,
                'detected_at' => now()->subDays(2),
            ]);

            Ticket::create([
                'title' => 'Mi calificación IA no se actualiza',
                'type' => 'cuenta',
                'level' => 'medium',
                'status' => 'in_progress',
                'description' => 'Ya analicé mi CV con la IA pero mi calificación sigue apareciendo en cero en el panel.',
                'created_by' => $diana->id,
                'affected_user_id' => $diana->id,
                'claimed_by' => $attendantId,
                'detected_at' => now()->subDays(5),
            ]);

            Ticket::create([
                'title' => 'Ayuda para recuperar mi contraseña',
                'type' => 'cuenta',
                'level' => 'low',
                'status' => 'closed',
                'description' => 'Olvidé mi contraseña y el correo de recuperación tardó en llegar, pero ya pude entrar.',
                'created_by' => $diana->id,
                'affected_user_id' => $diana->id,
                'claimed_by' => $attendantId,
                'closed_by' => $diana->id,
                'closed_at' => now()->subDay(),
                'detected_at' => now()->subDays(10),
            ]);
        }

        if ($lucas) {
            Ticket::create([
                'title' => 'No puedo publicar una vacante nueva',
                'type' => 'vacantes',
                'level' => 'high',
                'status' => 'open',
                'description' => 'Al guardar una vacante nueva el formulario se queda cargando y no se publica la oferta.',
                'created_by' => $lucas->id,
                'affected_user_id' => $lucas->id,
                'detected_at' => now()->subDay(),
            ]);

            Ticket::create([
                'title' => 'Duda sobre los planes de publicación',
                'type' => 'general',
                'level' => 'low',
                'status' => 'in_progress',
                'description' => 'Quisiera saber cuántas vacantes puedo publicar al mes con mi cuenta de empresa.',
                'created_by' => $lucas->id,
                'affected_user_id' => $lucas->id,
                'claimed_by' => $attendantId,
                'detected_at' => now()->subDays(7),
            ]);
        }

        $others = User::query()->whereNotIn('email', ['diana@unach.mx', 'lucas@unach.mx'])->inRandomOrder()->take(4)->get();

        foreach ($others as $index => $user) {
            Ticket::factory()->create([
                'created_by' => $user->id,
                'affected_user_id' => $user->id,
                'status' => 'open',
                'detected_at' => now()->subDays($index + 1),
            ]);
        }
    }
}
