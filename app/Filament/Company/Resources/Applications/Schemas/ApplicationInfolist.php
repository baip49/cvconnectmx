<?php

namespace App\Filament\Company\Resources\Applications\Schemas;

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
                        TextEntry::make('candidate.workExperiences')
                            ->label('')
                            ->columnSpanFull()
                            ->formatStateUsing(function ($record) {
                                $experiences = $record->candidate->workExperiences;

                                if ($experiences->isEmpty()) {
                                    return 'Sin experiencia laboral registrada';
                                }

                                return $experiences->map(function ($exp) {
                                    $dates = $exp->start_date
                                        ? $exp->start_date.' → '.($exp->end_date ?? 'Actualidad')
                                        : '';

                                    return "**{$exp->job_title}** en {$exp->company_name}".($dates ? " ({$dates})" : '');
                                })->implode("\n\n");
                            })
                            ->markdown(),
                    ]),

                Section::make('Educación')
                    ->schema([
                        TextEntry::make('candidate.educations')
                            ->label('')
                            ->columnSpanFull()
                            ->formatStateUsing(function ($record) {
                                $educations = $record->candidate->educations;

                                if ($educations->isEmpty()) {
                                    return 'Sin educación registrada';
                                }

                                return $educations->map(function ($edu) {
                                    return "**{$edu->degree}** — {$edu->institution}";
                                })->implode("\n\n");
                            })
                            ->markdown(),
                    ]),

                Section::make('Habilidades')
                    ->schema([
                        TextEntry::make('candidate.skills')
                            ->label('')
                            ->columnSpanFull()
                            ->formatStateUsing(function ($record) {
                                $skills = $record->candidate->skills;

                                if ($skills->isEmpty()) {
                                    return 'Sin habilidades registradas';
                                }

                                return $skills->map(function ($skill) {
                                    return $skill->level
                                        ? "• **{$skill->name}** ({$skill->level})"
                                        : "• {$skill->name}";
                                })->implode("\n");
                            })
                            ->markdown(),
                    ]),
            ]);
    }
}
