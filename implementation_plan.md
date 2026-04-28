CVConnectMX - Implementation Plan

This implementation plan details the strategy for building the complete CVConnectMX platform as outlined in the requeriments.md guide. The system is split into three portals: Candidate (Flux UI + Livewire), Company (Filament B2B), and Super Admin (Filament).

User Review Required

WARNING

This is a very large undertaking. I propose we tackle this in separate phases, starting with the Foundation (Models and Migrations) and Authentication. Please review the plan below and let me know if you approve this phased approach or if you'd prefer to focus on a specific phase first.

Open Questions

IMPORTANT

The requirements mention mapping relationships "según el archivo .sql proporcionado" (according to the provided .sql file). I couldn't find an .sql file in the repository root. I did find the migration files recently added to database/migrations/. Should I use these migration files as the source of truth for the schema and relationships?

Proposed Changes

Phase 1: Foundation (Models, Relationships, Observers)

Eloquent Models: Generate missing models (Candidate, Company, Vacancy, WorkExperience, Skill, Education, Application, CvAccess, AuditLog, Incident, IncidentAction, SystemAlert, BackupLog, Training, UserTraining).

Relationships: Map all relationships (e.g., User hasOne Candidate, Company hasMany Vacancies, etc.) based on the migration schema.

Audit Logs: Implement an Observer for key models to automatically record events in audit_logs.

Phase 2: Authentication & Registration (Fortify)

Registration Form: Modify Fortify's registration flow to include a "Candidate" or "Company" selector using Flux UI.

Backend Logic: On user creation, based on the selection, create the corresponding Candidate or Company profile and assign the correct role_id.

Custom Redirects: Configure dynamic post-login redirects to send users to their appropriate portal (Livewire Dashboard for Candidates, Filament /company for Companies, Filament /admin for Admins).

Phase 3: Candidate Portal (Filament)

Middleware: Protect candidate routes with a custom middleware.

Dashboard: Create a component for the candidate dashboard (applications summary, suggested vacancies).

Profile (Mi CV): Build a multi-section profile editor using Flux UI components for candidates, work_experiencies, educations, and skills.

Vacancy Search & Application: Develop a Livewire component to search vacancies and submit applications.

Phase 4: Company Portal (Filament B2B)

Panel Provider: Create CompanyPanelProvider for Filament at path /company.

Resources:

CompanyResource: For managing the company profile.

VacancyResource: CRUD operations for job offers.

ApplicationResource: A board/table to review candidates and update their status.

CV Viewing: Implement a feature to view candidate profiles, ensuring each view creates a record in cv_accesses.

Phase 5: Super Admin Portal (Filament Admin)

Panel Provider: Create AdminPanelProvider for Filament at path /admin (or update existing default panel).

Resources:

User & Access Management (UserResource, RoleResource, PermissionResource).

Directory View (Read-Only access to Candidates and Companies).

Logs & Audit (AuditLogResource, BackupLogResource, LoginAttemptResource).

Incidents & Security (IncidentResource, SystemAlertResource).

Training Management (TrainingResource, UserTrainingResource).

Verification Plan

Automated Tests

Write Pest unit and feature tests to verify that roles and profiles are correctly assigned upon registration.

Write tests to ensure portal access is restricted by role (Middleware tests).

Manual Verification

Manually register as a Candidate, complete a profile, and apply to a vacancy.

Manually register as a Company, create a vacancy, and review applications.

Verify Filament panel separation and access controls.
