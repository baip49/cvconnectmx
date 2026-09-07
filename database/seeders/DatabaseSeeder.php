<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            CandidateSeeder::class,
            CompanySeeder::class,
            SeedLucasVacancies::class,
            VacancySeeder::class,
            EducationSeeder::class,
            WorkExperienceSeeder::class,
            SkillSeeder::class,
            ApplicationSeeder::class,
            TrainingSeeder::class,
            UserTrainingSeeder::class,
            CandidateDocumentSeeder::class,
            CvAccessSeeder::class,
            PasswordHistorySeeder::class,
            LoginAttemptSeeder::class,
            AuditLogSeeder::class,
            IncidentSeeder::class,
            IncidentActionSeeder::class,
            SystemAlertSeeder::class,
            BackupLogSeeder::class,
            UserPermissionSeeder::class,
        ]);
    }
}
