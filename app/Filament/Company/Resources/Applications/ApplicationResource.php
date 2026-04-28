<?php

namespace App\Filament\Company\Resources\Applications;

use App\Filament\Company\Resources\Applications\Pages\ListApplications;
use App\Filament\Company\Resources\Applications\Pages\ViewApplication;
use App\Filament\Company\Resources\Applications\Schemas\ApplicationForm;
use App\Filament\Company\Resources\Applications\Schemas\ApplicationInfolist;
use App\Filament\Company\Resources\Applications\Tables\ApplicationsTable;
use App\Models\Application;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Postulaciones';

    protected static ?string $pluralModelLabel = 'Postulaciones';

    protected static ?string $modelLabel = 'Postulación';

    public static function form(Schema $schema): Schema
    {
        return ApplicationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ApplicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApplicationsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('vacancy', function (Builder $query) {
                $query->where('company_id', Auth::user()->company?->id);
            });
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
            'index' => ListApplications::route('/'),
            'view' => ViewApplication::route('/{record}'),
        ];
    }
}
