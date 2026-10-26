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
                Section::make(__('company.applications.estado_de_la_postulacion'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('vacancy.title')
                                    ->label(__('company.applications.vacante')),
                                TextEntry::make('status')
                                    ->label(__('company.applications.estado'))
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'pending' => __('company.applications.pendiente'),
                                        'interview' => __('company.applications.entrevista'),
                                        'accepted' => __('company.applications.aceptado'),
                                        'rejected' => __('company.applications.rechazado'),
                                        'offered' => __('company.applications.ofertado'),
                                        default => $state,
                                    })
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'interview' => 'info',
                                        'accepted' => 'success',
                                        'rejected' => 'danger',
                                        'offered' => 'primary',
                                        default => 'gray',
                                    }),
                                TextEntry::make('created_at')
                                    ->label(__('company.applications.fecha_de_postulacion'))
                                    ->dateTime(),
                            ]),
                        TextEntry::make('cover_letter')
                            ->label(__('company.applications.carta_de_presentacion'))
                            ->columnSpanFull()
                            ->default(__('company.applications.sin_carta_de_presentacion')),
                        TextEntry::make('internal_notes')
                            ->label(__('company.applications.notas_internas'))
                            ->columnSpanFull()
                            ->default('—'),
                    ]),

                Section::make(__('company.applications.perfil_del_candidato'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('candidate.user.name')
                                    ->label(__('company.applications.nombre')),
                                TextEntry::make('candidate.user.email')
                                    ->label(__('company.applications.correo_electronico')),
                                TextEntry::make('candidate.phone_encrypted')
                                    ->label(__('company.applications.telefono'))
                                    ->default('No proporcionado'),
                            ]),
                        TextEntry::make('candidate.summary')
                            ->label(__('company.applications.sobre_el_candidato'))
                            ->columnSpanFull()
                            ->default(__('company.applications.sin_descripcion')),
                    ]),

                Section::make(__('company.applications.experiencia_laboral'))
                    ->schema([
                        RepeatableEntry::make('candidate.workExperiences')
                            ->label('')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('job_title')
                                            ->label(__('company.applications.puesto'))
                                            ->weight('bold'),
                                        TextEntry::make('company_name')
                                            ->label(__('company.applications.empresa')),
                                        TextEntry::make('start_date')
                                            ->label(__('company.applications.desde'))
                                            ->date(),
                                        TextEntry::make('end_date')
                                            ->label(__('company.applications.hasta'))
                                            ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('Y-m-d') : __('company.applications.actualidad')),
                                    ]),
                            ])
                            ->columnSpanFull()
                            ->grid(2),
                    ]),

                Section::make(__('company.applications.educacion'))
                    ->schema([
                        RepeatableEntry::make('candidate.educations')
                            ->label('')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('degree')
                                            ->label(__('company.applications.grado_titulo'))
                                            ->weight('bold'),
                                        TextEntry::make('institution')
                                            ->label(__('company.applications.institucion')),
                                    ]),
                            ])
                            ->columnSpanFull()
                            ->grid(2),
                    ]),

                Section::make(__('company.applications.habilidades'))
                    ->schema([
                        TextEntry::make('skills')
                            ->label('')
                            ->columnSpanFull()
                            ->badge()
                            ->state(fn ($record) => $record->candidate->skills->map(fn ($skill) => "{$skill->name} ({$skill->level})"))
                            ->color('info'),
                    ]),

                Section::make(__('company.applications.documentos_adjuntos'))
                    ->schema([
                        RepeatableEntry::make('candidate.documents')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('name')
                                    ->hiddenLabel()
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
