<?php

namespace App\Filament\Company\Resources\Candidates\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CandidateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('company.candidates.informacion_del_candidato'))
                    ->schema([
                        TextEntry::make('user.name')
                            ->label(__('company.candidates.nombre')),
                        TextEntry::make('professional_title')
                            ->label(__('company.candidates.titulo_profesional')),
                        TextEntry::make('ai_rating')
                            ->label(__('company.candidates.rating_ia'))
                            ->badge()
                            ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                            ->suffix('/100'),
                        TextEntry::make('ai_analysis_summary')
                            ->label(__('company.candidates.resumen_de_analisis_ia'))
                            ->columnSpanFull()
                            ->visible(fn ($record) => ! empty($record->ai_analysis_summary)),
                    ])->columns(3),
            ]);
    }
}
