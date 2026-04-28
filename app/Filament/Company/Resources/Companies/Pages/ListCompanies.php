<?php

namespace App\Filament\Company\Resources\Companies\Pages;

use App\Filament\Company\Resources\Companies\CompanyResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyResource::class;

    public function mount(): void
    {
        $company = Auth::user()->company;

        if ($company) {
            $this->redirect(CompanyResource::getUrl('edit', ['record' => $company->id]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
