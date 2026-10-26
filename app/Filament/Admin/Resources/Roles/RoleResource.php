<?php

namespace App\Filament\Admin\Resources\Roles;

use App\Filament\Admin\Resources\Roles\Pages\CreateRole;
use App\Filament\Admin\Resources\Roles\Pages\EditRole;
use App\Filament\Admin\Resources\Roles\Pages\ListRoles;
use App\Filament\Admin\Resources\Roles\Pages\ViewRole;
use App\Filament\Admin\Resources\Roles\Schemas\RoleForm;
use App\Filament\Admin\Resources\Roles\Schemas\RoleInfolist;
use App\Filament\Admin\Resources\Roles\Tables\RolesTable;
use App\Models\Role;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    public static function getModelLabel(): string
    {
        return __('admin.roles.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.roles.plural_model_label');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation_groups.acceso');
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->hasPermission('roles.manage');
    }

    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->hasPermission('roles.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) auth()->user()?->hasPermission('roles.manage');
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) auth()->user()?->hasPermission('roles.manage');
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
