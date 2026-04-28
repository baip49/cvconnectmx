<?php

namespace App\Filament\Candidate\Resources\Vacancies;

use App\Filament\Candidate\Resources\Vacancies\Pages\ListVacancies;
use App\Filament\Candidate\Resources\Vacancies\Pages\ViewVacancy;
use App\Filament\Candidate\Resources\Vacancies\Schemas\VacancyForm;
use App\Filament\Candidate\Resources\Vacancies\Schemas\VacancyInfolist;
use App\Filament\Candidate\Resources\Vacancies\Tables\VacanciesTable;
use App\Models\Vacancy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VacancyResource extends Resource
{
    protected static ?string $model = Vacancy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Buscador de Empleos';

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
            'view' => ViewVacancy::route('/{record}'),
        ];
    }
}
