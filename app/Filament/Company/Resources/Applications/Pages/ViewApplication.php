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
                ->label(__('company.applications.cambiar_estado'))
                ->color('primary')
                ->icon('heroicon-o-chevron-up-down')
                ->visible(fn (): bool => (bool) Auth::user()?->hasPermission('applications.manage'))
                ->form([
                    Select::make('status')
                        ->label(__('company.applications.nuevo_estado'))
                        ->options([
                            'pending' => __('company.applications.pendiente'),
                            'interview' => __('company.applications.entrevista'),
                            'accepted' => __('company.applications.aceptado'),
                            'rejected' => __('company.applications.rechazado'),
                        ])
                        ->default(fn () => $this->getRecord()->status)
                        ->required(),
                    Textarea::make('internal_notes')
                        ->label(__('company.applications.notas_internas'))
                        ->default(fn () => $this->getRecord()->internal_notes)
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->getRecord()->update($data);
                    Notification::make()
                        ->title(__('company.applications.estado_actualizado_correctamente'))
                        ->success()
                        ->send();
                    $this->refreshFormData(['status', 'internal_notes']);
                }),
        ];
    }
}
