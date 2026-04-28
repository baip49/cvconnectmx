<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\CandidatePanelProvider;
use App\Providers\Filament\CompanyPanelProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    CandidatePanelProvider::class,
    CompanyPanelProvider::class,
    FortifyServiceProvider::class,
];
