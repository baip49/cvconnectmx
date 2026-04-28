<?php

namespace App\Filament\Company\Resources\Vacancies\Pages;

use App\Filament\Company\Resources\Vacancies\VacancyResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateVacancy extends CreateRecord
{
    protected static string $resource = VacancyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Auth::user()->company->id;
        $data['status'] = 'published';
        $data['published_at'] = now();

        return $data;
    }
}
