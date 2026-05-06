<?php

namespace App\Filament\Candidate\Pages;

use App\Models\Candidate;
use App\Models\CandidateDocument;
use App\Services\AiService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
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
                'cv_url' => $candidate->cv_url,
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
                Section::make('CV Principal')
                    ->description('Sube tu currículum en formato PDF para que nuestra IA (AWS) evalúe tu perfil.')
                    ->schema([
                        FileUpload::make('cv_url')
                            ->label('Archivo CV')
                            ->disk('s3')
                            ->directory('candidate-cvs')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120) // 5MB
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                    \Illuminate\Support\Facades\Log::info("MyCv: Storing file to S3 manually...");
                                    $path = $state->store('candidate-cvs', 's3');
                                    
                                    $candidate = Auth::user()->candidate;
                                    if ($candidate) {
                                        $candidate->cv_url = $path;
                                        $candidate->save();
                                        \Illuminate\Support\Facades\Log::info("MyCv: Candidate updated with permanent S3 path: {$path}");
                                        Notification::make()->title('CV subido y guardado en la nube')->success()->send();
                                    }
                                }
                            })
                            ->hintAction(
                                fn () => Auth::user()->candidate->cv_url ? Action::make('analyze_cv')
                                    ->label('Analizar con IA')
                                    ->icon('heroicon-o-sparkles')
                                    ->action(function (AiService $aiService) {
                                        $candidate = Auth::user()->candidate;
                                        $state = $candidate->cv_url;
                                        
                                        // $state es la ruta relativa en el disco (S3)
                                        $result = $aiService->analyzeCandidate($candidate, $state, 's3');
                                        
                                        $candidate->update([
                                            'ai_rating' => $result['rating'],
                                            'ai_analysis_summary' => $result['summary'],
                                        ]);

                                        Notification::make()
                                            ->title('Análisis completado')
                                            ->body("Tu perfil ha sido calificado con un " . $result['rating'] . "/100")
                                            ->success()
                                            ->send();
                                    }) : null
                            ),
                    ]),

                Section::make('Resumen Profesional')
                    ->description(fn() => Auth::user()->candidate->ai_rating ? "Tu perfil tiene una calificación de IA de " . Auth::user()->candidate->ai_rating . "/100" : "Analiza tu CV principal para obtener una calificación de IA.")
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
                                Hidden::make('id'),
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
                                Hidden::make('id'),
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
                                Hidden::make('id'),
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
                                    ->disk('s3')
                                    ->directory('candidate-documents')
                                    ->visibility('private')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $get) {
                                        if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile && $get('name')) {
                                            $path = $state->store('candidate-documents', 's3');
                                            
                                            Auth::user()->candidate->documents()->updateOrCreate(
                                                ['file_path' => $path],
                                                ['name' => $get('name')]
                                            );
                                            Notification::make()->title('Documento guardado en la nube')->success()->send();
                                        }
                                    })
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
            'cv_url' => $data['cv_url'] ?? null,
            'summary' => $data['summary'],
            'phone_encrypted' => $data['phone_encrypted'],
        ]);

        // Repeaters with relationships handle their own updates if relationship() is used.
        // However, since this is a custom page and we are using statePath('data'),
        // we might need to handle them manually if not using a Resource.
        // But Filament repeaters with relationship() work best inside Resources.
        // I will simplify and handle them via the candidate model relationships.

        // Sincronizar Experiencia Laboral
        $currentWorkIds = collect($data['work_experiences'])->pluck('id')->filter()->toArray();
        $candidate->workExperiences()->whereNotIn('id', $currentWorkIds)->delete();
        foreach ($data['work_experiences'] as $exp) {
            $candidate->workExperiences()->updateOrCreate(['id' => $exp['id'] ?? null], $exp);
        }

        // Sincronizar Educación
        $currentEduIds = collect($data['educations'])->pluck('id')->filter()->toArray();
        $candidate->educations()->whereNotIn('id', $currentEduIds)->delete();
        foreach ($data['educations'] as $edu) {
            $candidate->educations()->updateOrCreate(['id' => $edu['id'] ?? null], $edu);
        }

        // Sincronizar Habilidades
        $currentSkillIds = collect($data['skills'])->pluck('id')->filter()->toArray();
        $candidate->skills()->whereNotIn('id', $currentSkillIds)->delete();
        foreach ($data['skills'] as $skill) {
            $candidate->skills()->updateOrCreate(['id' => $skill['id'] ?? null], $skill);
        }

        // Sincronizar Documentos (Crucial para no borrar archivos del disco)
        $newDocPaths = collect($data['documents'])->pluck('file_path')->filter()->toArray();
        // Borrar solo los que ya no están en la lista (esto activará el observer para borrar el archivo del disco de los eliminados)
        $candidate->documents()->whereNotIn('file_path', $newDocPaths)->delete();

        foreach ($data['documents'] as $doc) {
            $candidate->documents()->updateOrCreate(
                ['file_path' => $doc['file_path']],
                ['name' => $doc['name']]
            );
        }

        Notification::make()
            ->title('CV actualizado correctamente')
            ->success()
            ->send();
    }
}
