<?php

namespace App\Filament\Admin\Resources\UserTrainings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserTrainingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.user_trainings.detalles_de_la_asignacion'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('user.name')
                                ->label(__('admin.user_trainings.usuario')),
                            TextEntry::make('training.title')
                                ->label(__('admin.user_trainings.capacitacion')),
                            TextEntry::make('status')
                                ->label(__('admin.user_trainings.estado'))
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'pending' => __('admin.user_trainings.pendiente'),
                                    'in_progress' => __('admin.user_trainings.en_progreso'),
                                    'completed' => __('admin.user_trainings.completada'),
                                    'expired' => __('admin.user_trainings.expirada'),
                                    default => $state,
                                })
                                ->color(fn (string $state): string => match ($state) {
                                    'pending' => 'gray',
                                    'in_progress' => 'info',
                                    'completed' => 'success',
                                    'expired' => 'danger',
                                    default => 'gray',
                                }),
                            TextEntry::make('score')
                                ->label(__('admin.user_trainings.calificacion'))
                                ->placeholder('N/A'),
                            TextEntry::make('completed_at')
                                ->label(__('admin.user_trainings.completado'))
                                ->dateTime('d/m/Y H:i')
                                ->placeholder(__('admin.user_trainings.pendiente')),
                            TextEntry::make('expires_at')
                                ->label(__('admin.user_trainings.expira'))
                                ->dateTime('d/m/Y H:i')
                                ->placeholder('N/A'),
                        ]),
                    ]),
            ]);
    }
}
