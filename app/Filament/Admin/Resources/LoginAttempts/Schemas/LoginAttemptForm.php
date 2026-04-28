<?php

namespace App\Filament\Admin\Resources\LoginAttempts\Schemas;

use Filament\Schemas\Schema;

class LoginAttemptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
