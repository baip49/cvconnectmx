<?php

namespace App\Filament\Company\Resources\Vacancies\Pages;

use App\Filament\Company\Resources\Vacancies\VacancyResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVacancy extends ViewRecord
{
    protected static string $resource = VacancyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
