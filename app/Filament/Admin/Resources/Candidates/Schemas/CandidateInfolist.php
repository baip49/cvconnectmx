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
                Section::make(__('admin.candidates.informacion_personal'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label(__('admin.candidates.nombre')),
                                TextEntry::make('user.last_name')
                                    ->label(__('admin.candidates.apellidos')),
                                TextEntry::make('user.email')
                                    ->label(__('admin.candidates.correo_electronico')),
                                TextEntry::make('phone_encrypted')
                                    ->label(__('admin.candidates.telefono')),
                                TextEntry::make('city')
                                    ->label(__('admin.candidates.ciudad')),
                                TextEntry::make('professional_title')
                                    ->label(__('admin.candidates.titulo_profesional')),
                                TextEntry::make('is_blocked')
                                    ->label(__('admin.candidates.estado'))
                                    ->badge()
                                    ->formatStateUsing(fn (bool $state) => $state ? __('admin.candidates.bloqueado') : __('admin.candidates.activo'))
                                    ->color(fn (bool $state) => $state ? 'danger' : 'success'),
                            ]),
                    ]),

                Section::make(__('admin.candidates.resumen_profesional'))
                    ->schema([
                        TextEntry::make('summary')
                            ->label('')
                            ->markdown()
                            ->default('Sin resumen profesional registrado'),
                    ]),

                Grid::make(2)
                    ->schema([
                        Section::make(__('admin.candidates.experiencia_laboral'))
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('workExperiences')
                                    ->label('')
                                    ->formatStateUsing(function ($record) {
                                        $experiences = $record->workExperiences;

                                        if ($experiences->isEmpty()) {
                                            return __('admin.candidates.sin_experiencia_registrada');
                                        }

                                        return $experiences->map(function ($exp) {
                                            $dates = $exp->start_date
                                                ? $exp->start_date.' → '.($exp->end_date ?? __('admin.candidates.actualidad'))
                                                : '';

                                            return __('admin.candidates.job_title_en_company_name_dates', ['job_title' => $exp->job_title, 'company_name' => $exp->company_name, 'dates' => $dates]);
                                        })->implode("\n\n");
                                    })
                                    ->markdown(),
                            ]),

                        Section::make(__('admin.candidates.educacion'))
                            ->columnSpan(1)
                            ->schema([
                                TextEntry::make('educations')
                                    ->label('')
                                    ->formatStateUsing(function ($record) {
                                        $educations = $record->educations;

                                        if ($educations->isEmpty()) {
                                            return __('admin.candidates.sin_educacion_registrada');
                                        }

                                        return $educations->map(function ($edu) {
                                            return "**{$edu->degree}**\n{$edu->institution}";
                                        })->implode("\n\n");
                                    })
                                    ->markdown(),
                            ]),
                    ]),

                Section::make(__('admin.candidates.habilidades'))
                    ->schema([
                        TextEntry::make('skills')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                $skills = $record->skills;

                                if ($skills->isEmpty()) {
                                    return __('admin.candidates.sin_habilidades_registradas');
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
