<?php

namespace App\Filament\Company\Resources\Vacancies;

use App\Filament\Company\Resources\Vacancies\Pages\CreateVacancy;
use App\Filament\Company\Resources\Vacancies\Pages\EditVacancy;
use App\Filament\Company\Resources\Vacancies\Pages\ListVacancies;
use App\Filament\Company\Resources\Vacancies\Pages\ViewVacancy;
use App\Filament\Company\Resources\Vacancies\Schemas\VacancyForm;
use App\Filament\Company\Resources\Vacancies\Schemas\VacancyInfolist;
use App\Filament\Company\Resources\Vacancies\Tables\VacanciesTable;
use App\Models\Vacancy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VacancyResource extends Resource
{
    protected static ?string $model = Vacancy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Mis Vacantes';

    protected static ?string $pluralModelLabel = 'Vacantes';

    protected static ?string $modelLabel = 'Vacante';

    public static function form(Schema $schema): Schema
    {
        return VacancyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VacancyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VacanciesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('company_id', Auth::user()->company?->id);
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
            'index' => ListVacancies::route('/'),
            'create' => CreateVacancy::route('/create'),
            'view' => ViewVacancy::route('/{record}'),
            'edit' => EditVacancy::route('/{record}/edit'),
        ];
    }
}
