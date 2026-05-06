<?php

namespace App\Filament\Company\Resources\Candidates\Schemas;

use Filament\Schemas\Schema;

class CandidateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\Section::make('Información del Candidato')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('user.name')
                            ->label('Nombre'),
                        \Filament\Infolists\Components\TextEntry::make('professional_title')
                            ->label('Título Profesional'),
                        \Filament\Infolists\Components\TextEntry::make('ai_rating')
                            ->label('Rating IA')
                            ->badge()
                            ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                            ->suffix('/100'),
                        \Filament\Infolists\Components\TextEntry::make('ai_analysis_summary')
                            ->label('Resumen de Análisis IA')
                            ->columnSpanFull()
                            ->visible(fn($record) => !empty($record->ai_analysis_summary)),
                    ])->columns(3),
            ]);
    }
}
