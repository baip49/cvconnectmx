<?php

namespace App\Filament\Admin\Resources\Candidates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CandidateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Nombre'),
                                TextEntry::make('user.last_name')
                                    ->label('Apellidos'),
                                TextEntry::make('user.email')
                                    ->label('Correo Electrónico'),
                                TextEntry::make('phone_encrypted')
                                    ->label('Teléfono'),
                                TextEntry::make('city')
                                    ->label('Ciudad'),
                                TextEntry::make('professional_title')
                                    ->label('Título Profesional'),
                            ]),
                    ]),

                Section::make('Resumen Profesional')
                    ->schema([
                        TextEntry::make('summary')
                            ->label('')
                            ->markdown()
                            ->default('Sin resumen profesional registrado'),
                    ]),

                Grid::make(2)
                    ->schema([
                        Section::make('Experiencia Laboral')
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('workExperiences')
                                    ->label('')
                                    ->formatStateUsing(function ($record) {
                                        $experiences = $record->workExperiences;

                                        if ($experiences->isEmpty()) {
                                            return 'Sin experiencia registrada';
                                        }

                                        return $experiences->map(function ($exp) {
                                            $dates = $exp->start_date
                                                ? $exp->start_date.' → '.($exp->end_date ?? 'Actualidad')
                                                : '';

                                            return "**{$exp->job_title}** en {$exp->company_name}\n{$dates}";
                                        })->implode("\n\n");
                                    })
                                    ->markdown(),
                            ]),

                        Section::make('Educación')
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('educations')
                                    ->label('')
                                    ->formatStateUsing(function ($record) {
                                        $educations = $record->educations;

                                        if ($educations->isEmpty()) {
                                            return 'Sin educación registrada';
                                        }

                                        return $educations->map(function ($edu) {
                                            return "**{$edu->degree}**\n{$edu->institution}";
                                        })->implode("\n\n");
                                    })
                                    ->markdown(),
                            ]),
                    ]),

                Section::make('Habilidades')
                    ->schema([
                        TextEntry::make('skills')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $skills = $record->skills;

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
