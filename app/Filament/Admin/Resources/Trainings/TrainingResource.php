<?php

namespace App\Filament\Admin\Resources\Trainings;

use App\Filament\Admin\Resources\Trainings\Pages\CreateTraining;
use App\Filament\Admin\Resources\Trainings\Pages\EditTraining;
use App\Filament\Admin\Resources\Trainings\Pages\ListTrainings;
use App\Filament\Admin\Resources\Trainings\Pages\ViewTraining;
use App\Filament\Admin\Resources\Trainings\Schemas\TrainingForm;
use App\Filament\Admin\Resources\Trainings\Schemas\TrainingInfolist;
use App\Filament\Admin\Resources\Trainings\Tables\TrainingsTable;
use App\Models\Training;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Capacitaciones';

    protected static string|UnitEnum|null $navigationGroup = 'Seguridad';

    public static function form(Schema $schema): Schema
    {
        return TrainingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingsTable::configure($table);
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
            'index' => ListTrainings::route('/'),
            'create' => CreateTraining::route('/create'),
            'view' => ViewTraining::route('/{record}'),
            'edit' => EditTraining::route('/{record}/edit'),
        ];
    }
}
