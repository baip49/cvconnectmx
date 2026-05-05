<?php

namespace App\Filament\Company\Resources\Applications\Schemas;

use Carbon\Carbon;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Estado de la Postulación')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('vacancy.title')
                                    ->label('Vacante'),
                                TextEntry::make('status')
                                    ->label('Estado')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'interview' => 'info',
                                        'accepted' => 'success',
                                        'rejected' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'pending' => 'Pendiente',
                                        'interview' => 'Entrevista',
                                        'accepted' => 'Aceptado',
                                        'rejected' => 'Rechazado',
                                        default => $state,
                                    }),
                                TextEntry::make('created_at')
                                    ->label('Fecha de Postulación')
                                    ->dateTime(),
                            ]),
                        TextEntry::make('cover_letter')
                            ->label('Carta de Presentación')
                            ->columnSpanFull()
                            ->default('Sin carta de presentación'),
                        TextEntry::make('internal_notes')
                            ->label('Notas Internas')
                            ->columnSpanFull()
                            ->default('—'),
                    ]),

                Section::make('Perfil del Candidato')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('candidate.user.name')
                                    ->label('Nombre'),
                                TextEntry::make('candidate.user.email')
                                    ->label('Correo Electrónico'),
                                TextEntry::make('candidate.phone_encrypted')
                                    ->label('Teléfono')
                                    ->default('No proporcionado'),
                            ]),
                        TextEntry::make('candidate.summary')
                            ->label('Sobre el Candidato')
                            ->columnSpanFull()
                            ->default('Sin descripción'),
                    ]),

                Section::make('Experiencia Laboral')
                    ->schema([
                        RepeatableEntry::make('candidate.workExperiences')
                            ->label('')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('job_title')
                                            ->label('Puesto')
                                            ->weight('bold'),
                                        TextEntry::make('company_name')
                                            ->label('Empresa'),
                                        TextEntry::make('start_date')
                                            ->label('Desde')
                                            ->date(),
                                        TextEntry::make('end_date')
                                            ->label('Hasta')
                                            ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('Y-m-d') : 'Actualidad'),
                                    ]),
                            ])
                            ->columnSpanFull()
                            ->grid(2),
                    ]),

                Section::make('Educación')
                    ->schema([
                        RepeatableEntry::make('candidate.educations')
                            ->label('')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('degree')
                                            ->label('Grado / Título')
                                            ->weight('bold'),
                                        TextEntry::make('institution')
                                            ->label('Institución'),
                                    ]),
                            ])
                            ->columnSpanFull()
                            ->grid(2),
                    ]),

                Section::make('Habilidades')
                    ->schema([
                        TextEntry::make('skills')
                            ->label('')
                            ->columnSpanFull()
                            ->badge()
                            ->state(fn ($record) => $record->candidate->skills->map(fn ($skill) => "{$skill->name} ({$skill->level})"))
                            ->color('info'),
                    ]),

                Section::make('Documentos Adjuntos')
                    ->schema([
                        RepeatableEntry::make('candidate.documents')
                            ->label('')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('')
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->url(fn ($record) => route('document.show', ['slug' => $record->slug]), shouldOpenInNewTab: true)
                                    ->color('primary')
                                    ->extraAttributes(['class' => 'hover:underline']),
                            ])
                            ->columnSpanFull()
                            ->grid(3),
                    ]),
            ]);
    }
}
