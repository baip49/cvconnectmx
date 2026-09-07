<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SeedLucasVacancies extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find user Lucas
        $lucas = User::where('name', 'like', '%lucas%')
            ->orWhere('email', 'like', '%lucas%')
            ->first();

        // Si no existe, buscamos el ID 2 (asumiendo que es él por el stack trace anterior)
        if (! $lucas) {
            $lucas = User::find(2);
        }

        if (! $lucas) {
            $this->command->error("Usuario 'lucas' no encontrado.");

            return;
        }

        $company = $lucas->company;

        if (! $company) {
            $this->command->error("El usuario 'lucas' no tiene una empresa asociada.");

            return;
        }

        if ($company->vacancies()->exists()) {
            return;
        }

        $vacancies = [
            [
                'title' => 'Desarrollador FullStack Laravel y React',
                'description' => 'Buscamos un ingeniero de software con al menos 3 años de experiencia trabajando con PHP, Laravel y React. Experiencia con AWS es un plus.',
                'requirements' => '- PHP 8.x
- Laravel 10 o superior
- React.js y TailwindCSS
- Git y CI/CD',
                'work_model' => 'remote',
                'min_salary' => 30000,
                'max_salary' => 45000,
                'show_salary' => true,
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'Especialista en Inteligencia Artificial (AWS Bedrock)',
                'description' => 'Únete a nuestro equipo de innovación para desarrollar soluciones basadas en GenAI utilizando Amazon Bedrock, Textract y Google Cloud Natural Language API.',
                'requirements' => '- Experiencia con Python o PHP
- Conocimientos en AWS Bedrock y modelos fundacionales (Claude 3, Titan)
- Experiencia en integración de APIs
- Nivel de inglés avanzado',
                'work_model' => 'hybrid',
                'min_salary' => 50000,
                'max_salary' => 70000,
                'show_salary' => true,
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'Diseñador UI/UX',
                'description' => 'Buscamos un diseñador creativo para liderar la renovación de nuestra plataforma de reclutamiento.',
                'requirements' => '- Dominio de Figma
- Experiencia creando sistemas de diseño
- Portfolio con proyectos web comprobables',
                'work_model' => 'on_site',
                'min_salary' => 25000,
                'max_salary' => 35000,
                'show_salary' => true,
                'status' => 'published',
                'published_at' => now(),
            ],
        ];

        foreach ($vacancies as $vacancy) {
            $company->vacancies()->create($vacancy);
        }

        $this->command->info('3 vacantes creadas exitosamente para la empresa de Lucas.');
    }
}
