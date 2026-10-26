<?php

namespace App\Filament\Candidate\Pages;

use App\Models\Application;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class Offers extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    public function getTitle(): string
    {
        return __('candidate.offers.ofertas_recibidas');
    }

    public static function getNavigationLabel(): string
    {
        return __('candidate.offers.ofertas');
    }

    protected string $view = 'filament.candidate.pages.offers';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Application::query()
                    ->where('candidate_id', Auth::user()->candidate->id)
                    ->where('is_offer', true)
            )
            ->columns([
                TextColumn::make('vacancy.title')
                    ->label(__('candidate.offers.vacante'))
                    ->searchable()
                    ->sortable()
                    ->url(fn (Application $record): string => route('filament.candidate.resources.vacancies.view', ['record' => $record->vacancy_id]))
                    ->color('primary')
                    ->weight('bold'),
                TextColumn::make('vacancy.company.name')
                    ->label(__('candidate.offers.empresa'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('candidate.offers.estado'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'offered' => __('candidate.offers.ofertado'),
                        'accepted' => __('candidate.offers.aceptado'),
                        'rejected' => __('candidate.offers.rechazado'),
                        'pending' => __('candidate.offers.pendiente'),
                        'interview' => __('candidate.offers.entrevista'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'offered' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        'interview' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label(__('candidate.offers.fecha'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('accept')
                    ->label(__('candidate.offers.aceptar'))
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (Application $record) => $record->status === 'offered')
                    ->requiresConfirmation()
                    ->modalHeading(__('candidate.offers.aceptar_oferta'))
                    ->modalDescription(__('candidate.offers.estas_seguro_de_que_deseas_aceptar_esta'))
                    ->modalSubmitActionLabel(__('candidate.offers.si_aceptar'))
                    ->action(function (Application $record) {
                        $record->update(['status' => 'accepted']);
                        Notification::make()
                            ->title(__('candidate.offers.oferta_aceptada'))
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label(__('candidate.offers.rechazar'))
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (Application $record) => $record->status === 'offered')
                    ->requiresConfirmation()
                    ->modalHeading(__('candidate.offers.rechazar_oferta'))
                    ->modalDescription(__('candidate.offers.estas_seguro_de_que_deseas_rechazar_esta'))
                    ->modalSubmitActionLabel(__('candidate.offers.si_rechazar'))
                    ->action(function (Application $record) {
                        $record->update(['status' => 'rejected']);
                        Notification::make()
                            ->title(__('candidate.offers.oferta_rechazada'))
                            ->danger()
                            ->send();
                    }),
            ]);
    }
}
