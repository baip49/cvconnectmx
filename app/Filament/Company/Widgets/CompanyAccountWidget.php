<?php

namespace App\Filament\Company\Widgets;

use Filament\Widgets\AccountWidget;

class CompanyAccountWidget extends AccountWidget
{
    protected static ?int $sort = -1;
    protected int | string | array $columnSpan = 'full';
}
