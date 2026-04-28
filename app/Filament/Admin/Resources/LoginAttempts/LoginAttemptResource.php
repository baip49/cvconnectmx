<?php

namespace App\Filament\Admin\Resources\LoginAttempts;

use App\Filament\Admin\Resources\LoginAttempts\Pages\CreateLoginAttempt;
use App\Filament\Admin\Resources\LoginAttempts\Pages\EditLoginAttempt;
use App\Filament\Admin\Resources\LoginAttempts\Pages\ListLoginAttempts;
use App\Filament\Admin\Resources\LoginAttempts\Pages\ViewLoginAttempt;
use App\Filament\Admin\Resources\LoginAttempts\Schemas\LoginAttemptForm;
use App\Filament\Admin\Resources\LoginAttempts\Schemas\LoginAttemptInfolist;
use App\Filament\Admin\Resources\LoginAttempts\Tables\LoginAttemptsTable;
use App\Models\LoginAttempt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LoginAttemptResource extends Resource
{
    protected static ?string $model = LoginAttempt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return LoginAttemptForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LoginAttemptInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoginAttemptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoginAttempts::route('/'),
            'create' => CreateLoginAttempt::route('/create'),
            'view' => ViewLoginAttempt::route('/{record}'),
            'edit' => EditLoginAttempt::route('/{record}/edit'),
        ];
    }
}
