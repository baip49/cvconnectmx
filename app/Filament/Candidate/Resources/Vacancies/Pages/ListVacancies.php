<?php

namespace App\Filament\Candidate\Resources\Vacancies\Pages;

use App\Filament\Candidate\Resources\Vacancies\VacancyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVacancies extends ListRecords
{
    protected static string $resource = VacancyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
