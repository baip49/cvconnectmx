<?php

namespace App\Filament\Admin\Resources\UserTrainings;

use App\Filament\Admin\Resources\UserTrainings\Pages\CreateUserTraining;
use App\Filament\Admin\Resources\UserTrainings\Pages\EditUserTraining;
use App\Filament\Admin\Resources\UserTrainings\Pages\ListUserTrainings;
use App\Filament\Admin\Resources\UserTrainings\Pages\ViewUserTraining;
use App\Filament\Admin\Resources\UserTrainings\Schemas\UserTrainingForm;
use App\Filament\Admin\Resources\UserTrainings\Schemas\UserTrainingInfolist;
use App\Filament\Admin\Resources\UserTrainings\Tables\UserTrainingsTable;
use App\Models\UserTraining;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserTrainingResource extends Resource
{
    protected static ?string $model = UserTraining::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return UserTrainingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserTrainingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserTrainingsTable::configure($table);
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
            'index' => ListUserTrainings::route('/'),
            'create' => CreateUserTraining::route('/create'),
            'view' => ViewUserTraining::route('/{record}'),
            'edit' => EditUserTraining::route('/{record}/edit'),
        ];
    }
}
