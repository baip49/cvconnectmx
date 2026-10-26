<?php

namespace App\Filament\Admin\Resources\Trainings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TrainingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.trainings.detalles_de_la_capacitacion'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('title')
                                ->label(__('admin.trainings.titulo')),
                            TextEntry::make('type')
                                ->label(__('admin.trainings.tipo'))
                                ->badge(),
                            TextEntry::make('targetRole.name')
                                ->label(__('admin.trainings.rol_objetivo'))
                                ->placeholder(__('admin.trainings.todos_los_roles')),
                            TextEntry::make('validity_days')
                                ->label(__('admin.trainings.validez'))
                                ->suffix(__('admin.trainings.dias')),
                            TextEntry::make('is_active')
                                ->label(__('admin.trainings.estado'))
                                ->badge()
                                ->formatStateUsing(fn (bool $state): string => $state ? __('admin.trainings.activo') : __('admin.trainings.inactivo'))
                                ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                            TextEntry::make('created_at')
                                ->label(__('admin.trainings.creado'))
                                ->dateTime('d/m/Y H:i'),
                        ]),
                    ]),
            ]);
    }
}
