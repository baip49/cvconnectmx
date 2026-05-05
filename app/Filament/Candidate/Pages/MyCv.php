<?php

namespace App\Filament\Candidate\Pages;

use App\Models\Candidate;
use App\Models\CandidateDocument;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class MyCv extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $title = 'Mi CV';

    protected static ?string $navigationLabel = 'Mi CV';

    protected string $view = 'filament.candidate.pages.my-cv';

    public ?array $data = [];

    public function mount(): void
    {
        $candidate = Auth::user()->candidate;

        if ($candidate) {
            $this->getSchema('form')->fill([
                'summary' => $candidate->summary,
                'phone_encrypted' => $candidate->phone_encrypted,
                'work_experiences' => $candidate->workExperiences->toArray(),
                'educations' => $candidate->educations->toArray(),
                'skills' => $candidate->skills->toArray(),
                'documents' => $candidate->documents->toArray(),
            ]);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Resumen Profesional')
                    ->schema([
                        Textarea::make('summary')
                            ->label('Sobre mí')
                            ->rows(3),
                        TextInput::make('phone_encrypted')
                            ->label('Teléfono')
                            ->tel(),
                    ]),

                Section::make('Experiencia Laboral')
                    ->schema([
                        Repeater::make('work_experiences')
                            ->label('')
                            ->schema([
                                TextInput::make('company_name')
                                    ->label('Empresa')
                                    ->required(),
                                TextInput::make('job_title')
                                    ->label('Puesto')
                                    ->required(),
                                DatePicker::make('start_date')
                                    ->label('Fecha de inicio'),
                                DatePicker::make('end_date')
                                    ->label('Fecha de fin'),
                            ])
                            ->columns(2),
                    ]),

                Section::make('Educación')
                    ->schema([
                        Repeater::make('educations')
                            ->label('')
                            ->schema([
                                TextInput::make('institution')
                                    ->label('Institución')
                                    ->required(),
                                TextInput::make('degree')
                                    ->label('Grado / Título')
                                    ->required(),
                            ])
                            ->columns(2),
                    ]),

                Section::make('Habilidades')
                    ->schema([
                        Repeater::make('skills')
                            ->label('')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Habilidad')
                                    ->required(),
                                TextInput::make('level')
                                    ->label('Nivel (Básico, Intermedio, Avanzado)'),
                            ])
                            ->columns(2),
                    ]),

                Section::make('Documentos Adjuntos')
                    ->description('Sube tu CV, certificados u otros archivos relevantes.')
                    ->schema([
                        Repeater::make('documents')
                            ->hiddenLabel()
                            ->compact()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre del Documento')
                                    ->placeholder('Ej: Certificado de Inglés, CV 2024')
                                    ->required(),
                                FileUpload::make('file_path')
                                    ->label('Archivo')
                                    ->disk('local')
                                    ->directory('candidate-documents')
                                    ->visibility('private')
                                    ->required()
                                    ->extraAttributes(['class' => 'mb-0'])
                                    ->hintAction(
                                        fn ($state, $record, $component) => $state ? Action::make('preview')
                                            ->label('Ver archivo actual')
                                            ->icon('heroicon-o-eye')
                                            ->url(function () use ($component) {
                                                $data = $component->getContainer()->getRawState();
                                                $document = CandidateDocument::where('file_path', $data['file_path'] ?? null)->first();

                                                return $document ? route('document.show', $document->slug) : null;
                                            }, shouldOpenInNewTab: true)
                                            ->visible(fn () => ! empty($state)) : null
                                    ),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar Cambios')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $candidate = Auth::user()->candidate;
        $data = $this->getSchema('form')->getState();

        $candidate->update([
            'summary' => $data['summary'],
            'phone_encrypted' => $data['phone_encrypted'],
        ]);

        // Repeaters with relationships handle their own updates if relationship() is used.
        // However, since this is a custom page and we are using statePath('data'),
        // we might need to handle them manually if not using a Resource.
        // But Filament repeaters with relationship() work best inside Resources.
        // I will simplify and handle them via the candidate model relationships.

        $candidate->workExperiences()->delete();
        foreach ($data['work_experiences'] as $exp) {
            $candidate->workExperiences()->create($exp);
        }

        $candidate->educations()->delete();
        foreach ($data['educations'] as $edu) {
            $candidate->educations()->create($edu);
        }

        $candidate->skills()->delete();
        foreach ($data['skills'] as $skill) {
            $candidate->skills()->create($skill);
        }

        foreach ($candidate->documents as $document) {
            $document->delete();
        }

        foreach ($data['documents'] as $doc) {
            $candidate->documents()->create($doc);
        }

        Notification::make()
            ->title('CV actualizado correctamente')
            ->success()
            ->send();
    }
}
