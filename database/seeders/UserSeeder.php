<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Education;
use App\Models\Skill;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\WorkExperience;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin Principal
        User::factory()
            ->admin()
            ->create([
                'email' => 'cesar@unach.mx',
                'password' => bcrypt('123456789'),
                'name' => 'César Enrique',
                'last_name' => 'Sánchez Montoya',
            ]);

        // 2. Empresa de Prueba
        User::factory()
            ->company()
            ->create([
                'email' => 'lucas@unach.mx',
                'password' => bcrypt('123456789'),
                'name' => 'Lucas Emanuel',
                'last_name' => 'Solís Hernández',
            ]);

        // 3. Candidato de Prueba con perfil completo
        $diana = User::factory()
            ->candidate()
            ->create([
                'email' => 'diana@unach.mx',
                'password' => bcrypt('123456789'),
                'name' => 'Diana Laura',
                'last_name' => 'Ruiz Castillo',
            ]);

        $this->seedCandidateProfile($diana->candidate);

        // 4. Generar 5 Empresas adicionales con vacantes
        User::factory(5)
            ->company()
            ->create()
            ->each(function ($user) {
                Vacancy::factory(3)->create([
                    'company_id' => $user->company->id,
                ]);
            });

        // 5. Generar 5 Candidatos adicionales con perfiles y postulaciones
        User::factory(5)
            ->candidate()
            ->create()
            ->each(function ($user) {
                $candidate = $user->candidate;
                $this->seedCandidateProfile($candidate);

                // Postularse a vacantes aleatorias
                $vacancies = Vacancy::inRandomOrder()->take(2)->get();
                foreach ($vacancies as $vacancy) {
                    Application::factory()->create([
                        'candidate_id' => $candidate->id,
                        'vacancy_id' => $vacancy->id,
                    ]);
                }
            });

        // 6. Generar algunos Audit Logs
        AuditLog::factory(10)->create();
    }

    /**
     * Helper to seed candidate profile data
     */
    private function seedCandidateProfile($candidate): void
    {
        WorkExperience::factory(2)->create(['candidate_id' => $candidate->id]);
        Education::factory(1)->create(['candidate_id' => $candidate->id]);
        Skill::factory(5)->create(['candidate_id' => $candidate->id]);
    }
}
