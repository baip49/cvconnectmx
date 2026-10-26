<?php

namespace App\Filament\Admin\Resources\Permissions;

use App\Filament\Admin\Resources\Permissions\Pages\CreatePermission;
use App\Filament\Admin\Resources\Permissions\Pages\EditPermission;
use App\Filament\Admin\Resources\Permissions\Pages\ListPermissions;
use App\Filament\Admin\Resources\Permissions\Pages\ViewPermission;
use App\Filament\Admin\Resources\Permissions\Schemas\PermissionForm;
use App\Filament\Admin\Resources\Permissions\Schemas\PermissionInfolist;
use App\Filament\Admin\Resources\Permissions\Tables\PermissionsTable;
use App\Models\Permission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    public static function getModelLabel(): string
    {
        return __('admin.permissions.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.permissions.plural_model_label');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation_groups.acceso');
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->hasPermission('permissions.manage');
    }

    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->hasPermission('permissions.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) auth()->user()?->hasPermission('permissions.manage');
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) auth()->user()?->hasPermission('permissions.manage');
    }

    public static function form(Schema $schema): Schema
    {
        return PermissionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PermissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermissionsTable::configure($table);
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
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'view' => ViewPermission::route('/{record}'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }
}
