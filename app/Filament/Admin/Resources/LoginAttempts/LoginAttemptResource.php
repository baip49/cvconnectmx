<?php

namespace App\Filament\Admin\Resources\LoginAttempts;

use App\Filament\Admin\Resources\LoginAttempts\Pages\ListLoginAttempts;
use App\Filament\Admin\Resources\LoginAttempts\Pages\ViewLoginAttempt;
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

    public static function getModelLabel(): string
    {
        return __('admin.login_attempts.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.login_attempts.plural_model_label');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;

    public static function getNavigationLabel(): string
    {
        return __('admin.login_attempts.intentos_de_acceso');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation_groups.seguridad');
    }

    public static function infolist(Schema $schema): Schema
    {
        return LoginAttemptInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoginAttemptsTable::configure($table);
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->isAdmin();
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
            'view' => ViewLoginAttempt::route('/{record}'),
        ];
    }
}
