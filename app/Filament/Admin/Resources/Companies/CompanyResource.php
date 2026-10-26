<?php

namespace App\Filament\Admin\Resources\Companies;

use App\Filament\Admin\Resources\Companies\Pages\ListCompanies;
use App\Filament\Admin\Resources\Companies\Pages\ViewCompany;
use App\Filament\Admin\Resources\Companies\Schemas\CompanyForm;
use App\Filament\Admin\Resources\Companies\Schemas\CompanyInfolist;
use App\Filament\Admin\Resources\Companies\Tables\CompaniesTable;
use App\Models\Company;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    public static function getModelLabel(): string
    {
        return __('admin.companies.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.companies.plural_model_label');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation_groups.directorios');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.companies.empresas');
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) auth()->user()?->hasPermission('companies.manage');
    }

    public static function form(Schema $schema): Schema
    {
        return CompanyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompanyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompaniesTable::configure($table);
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
            'index' => ListCompanies::route('/'),
            'view' => ViewCompany::route('/{record}'),
        ];
    }
}
