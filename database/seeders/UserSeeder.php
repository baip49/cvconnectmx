<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Education;
use App\Models\Role;
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
        if (! User::query()->where('email', 'cesar@unach.mx')->exists()) {
            User::factory()->admin()->create([
                'email' => 'cesar@unach.mx',
                'password' => bcrypt('123456789'),
                'name' => 'César Enrique',
                'last_name' => 'Sánchez Montoya',
            ]);
        }

        // 2. Empresa de Prueba
        if (! User::query()->where('email', 'lucas@unach.mx')->exists()) {
            User::factory()->company()->create([
                'email' => 'lucas@unach.mx',
                'password' => bcrypt('123456789'),
                'name' => 'Lucas Emanuel',
                'last_name' => 'Solís Hernández',
            ]);
        }

        // 3. Candidato de Prueba con perfil completo
        $diana = User::query()->where('email', 'diana@unach.mx')->first();

        if (! $diana) {
            $diana = User::factory()->candidate()->create([
                'email' => 'diana@unach.mx',
                'password' => bcrypt('123456789'),
                'name' => 'Diana Laura',
                'last_name' => 'Ruiz Castillo',
            ]);
        }

        $this->seedCandidateProfile($diana->candidate);

        // 4. Generar 5 Empresas adicionales con vacantes
        $companyRoleId = Role::query()->where('name', 'company')->value('id');
        $companiesToCreate = max(0, 6 - User::query()->where('role_id', $companyRoleId)->count());

        User::factory($companiesToCreate)
            ->company()
            ->create()
            ->each(function ($user) {
                Vacancy::factory(3)->create([
                    'company_id' => $user->company->id,
                ]);
            });

        // 5. Generar 5 Candidatos adicionales con perfiles y postulaciones
        $candidateRoleId = Role::query()->where('name', 'candidate')->value('id');
        $candidatesToCreate = max(0, 6 - User::query()->where('role_id', $candidateRoleId)->count());

        User::factory($candidatesToCreate)
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

    }

    /**
     * Helper to seed candidate profile data
     */
    private function seedCandidateProfile($candidate): void
    {
        if (! $candidate->workExperiences()->exists()) {
            WorkExperience::factory(2)->create(['candidate_id' => $candidate->id]);
        }

        if (! $candidate->educations()->exists()) {
            Education::factory()->create(['candidate_id' => $candidate->id]);
        }

        if (! $candidate->skills()->exists()) {
            Skill::factory(5)->create(['candidate_id' => $candidate->id]);
        }
    }
}
