<?php

namespace App\Filament\Admin\Resources\CandidateDocuments;

use App\Filament\Admin\Resources\CandidateDocuments\Pages\CreateCandidateDocument;
use App\Filament\Admin\Resources\CandidateDocuments\Pages\EditCandidateDocument;
use App\Filament\Admin\Resources\CandidateDocuments\Pages\ListCandidateDocuments;
use App\Filament\Admin\Resources\CandidateDocuments\Pages\ViewCandidateDocument;
use App\Filament\Admin\Resources\CandidateDocuments\Schemas\CandidateDocumentForm;
use App\Filament\Admin\Resources\CandidateDocuments\Schemas\CandidateDocumentInfolist;
use App\Filament\Admin\Resources\CandidateDocuments\Tables\CandidateDocumentsTable;
use App\Models\CandidateDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CandidateDocumentResource extends Resource
{
    protected static ?string $model = CandidateDocument::class;

    protected static ?string $navigationLabel = 'Archivos';

    protected static ?string $pluralModelLabel = 'Archivos';

    protected static ?string $modelLabel = 'Archivo';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    public static function form(Schema $schema): Schema
    {
        return CandidateDocumentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CandidateDocumentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CandidateDocumentsTable::configure($table);
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
            'index' => ListCandidateDocuments::route('/'),
            'create' => CreateCandidateDocument::route('/create'),
            'view' => ViewCandidateDocument::route('/{record}'),
            'edit' => EditCandidateDocument::route('/{record}/edit'),
        ];
    }
}
