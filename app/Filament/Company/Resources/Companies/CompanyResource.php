<?php

namespace App\Filament\Company\Resources\Companies;

use App\Filament\Company\Resources\Companies\Pages\CreateCompany;
use App\Filament\Company\Resources\Companies\Pages\EditCompany;
use App\Filament\Company\Resources\Companies\Pages\ListCompanies;
use App\Filament\Company\Resources\Companies\Pages\ViewCompany;
use App\Filament\Company\Resources\Companies\Schemas\CompanyForm;
use App\Filament\Company\Resources\Companies\Schemas\CompanyInfolist;
use App\Filament\Company\Resources\Companies\Tables\CompaniesTable;
use App\Models\Company;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Mi Empresa';

    protected static ?string $pluralModelLabel = 'Empresas';

    protected static ?string $modelLabel = 'Empresa';

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::user()->id);
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
            'create' => CreateCompany::route('/create'),
            'view' => ViewCompany::route('/{record}'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }
}
