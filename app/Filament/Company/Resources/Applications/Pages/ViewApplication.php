<?php

namespace App\Filament\Company\Resources\Applications\Pages;

use App\Filament\Company\Resources\Applications\ApplicationResource;
use App\Models\Application;
use App\Models\CvAccess;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        /** @var Application $application */
        $application = $this->getRecord();

        CvAccess::create([
            'candidate_id' => $application->candidate_id,
            'accessed_by' => Auth::id(),
            'application_id' => $application->id,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('change_status')
                ->label('Cambiar Estado')
                ->color('primary')
                ->icon('heroicon-o-chevron-up-down')
                ->form([
                    Select::make('status')
                        ->label('Nuevo Estado')
                        ->options([
                            'pending' => 'Pendiente',
                            'interview' => 'Entrevista',
                            'accepted' => 'Aceptado',
                            'rejected' => 'Rechazado',
                        ])
                        ->default(fn () => $this->getRecord()->status)
                        ->required(),
                    Textarea::make('internal_notes')
                        ->label('Notas Internas')
                        ->default(fn () => $this->getRecord()->internal_notes)
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->getRecord()->update($data);
                    Notification::make()
                        ->title('Estado actualizado correctamente')
                        ->success()
                        ->send();
                    $this->refreshFormData(['status', 'internal_notes']);
                }),
        ];
    }
}
