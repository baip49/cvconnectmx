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

    protected static ?string $title = 'Ofertas Recibidas';

    protected static ?string $navigationLabel = 'Ofertas';

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
                    ->label('Vacante')
                    ->searchable()
                    ->sortable()
                    ->url(fn (Application $record): string => route('filament.candidate.resources.vacancies.view', ['record' => $record->vacancy_id]))
                    ->color('primary')
                    ->weight('bold'),
                TextColumn::make('vacancy.company.name')
                    ->label('Empresa')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'offered' => 'Ofertado',
                        'accepted' => 'Aceptado',
                        'rejected' => 'Rechazado',
                        'pending' => 'Pendiente',
                        'interview' => 'Entrevista',
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
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('accept')
                    ->label('Aceptar')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (Application $record) => $record->status === 'offered')
                    ->requiresConfirmation()
                    ->modalHeading('Aceptar Oferta')
                    ->modalDescription('¿Estás seguro de que deseas aceptar esta oferta de trabajo? Al hacerlo, la empresa será notificada.')
                    ->modalSubmitActionLabel('Sí, aceptar')
                    ->action(function (Application $record) {
                        $record->update(['status' => 'accepted']);
                        Notification::make()
                            ->title('Oferta aceptada')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Rechazar')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (Application $record) => $record->status === 'offered')
                    ->requiresConfirmation()
                    ->modalHeading('Rechazar Oferta')
                    ->modalDescription('¿Estás seguro de que deseas rechazar esta oferta de trabajo? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Sí, rechazar')
                    ->action(function (Application $record) {
                        $record->update(['status' => 'rejected']);
                        Notification::make()
                            ->title('Oferta rechazada')
                            ->danger()
                            ->send();
                    }),
            ]);
    }
}
