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
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MyCv extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public function getTitle(): string
    {
        return __('candidate.my_cv.mi_cv');
    }

    public static function getNavigationLabel(): string
    {
        return __('candidate.my_cv.mi_cv');
    }

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
                Section::make(__('candidate.my_cv.cv_principal'))
                    ->description(__('candidate.my_cv.sube_tu_curriculum_en_formato_pdf_para_q'))
                    ->schema([
                        FileUpload::make('cv_url')
                            ->label(__('candidate.my_cv.archivo_cv'))
                            ->disk('local')
                            ->directory('candidate-cvs')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120) // 5MB
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                if ($state instanceof TemporaryUploadedFile) {
                                    // The file is stored before form validation runs, so check it here.
                                    if ($state->getMimeType() !== 'application/pdf' || $state->getSize() > 5120 * 1024) {
                                        Notification::make()->title(__('candidate.my_cv.archivo_no_permitido'))->body(__('candidate.my_cv.el_cv_debe_ser_un_pdf_de_maximo_5mb'))->danger()->send();

                                        return;
                                    }

                                    Log::info('MyCv: Storing file to S3 manually...');
                                    $path = $state->store('candidate-cvs', 'local');

                                    $candidate = Auth::user()->candidate;
                                    if ($candidate) {
                                        $candidate->cv_url = $path;
                                        $candidate->save();
                                        Log::info("MyCv: Candidate updated with permanent S3 path: {$path}");
                                        Notification::make()->title(__('candidate.my_cv.cv_subido_y_guardado_en_la_nube'))->success()->send();
                                    }
                                }
                            })
                            ->hintAction(
                                fn () => Auth::user()->candidate->cv_url ? Action::make('analyze_cv')
                                    ->label(__('candidate.my_cv.analizar_con_ia'))
                                    ->icon('heroicon-o-sparkles')
                                    ->action(function (AiService $aiService) {
                                        $candidate = Auth::user()->candidate;
                                        $state = $candidate->cv_url;

                                        // $state es la ruta relativa en el disco (S3)
                                        $result = $aiService->analyzeCandidate($candidate);

                                        $candidate->update([
                                            'ai_rating' => $result['rating'],
                                            'ai_analysis_summary' => $result['summary'],
                                        ]);

                                        Notification::make()
                                            ->title(__('candidate.my_cv.analisis_completado'))
                                            ->body(__('candidate.my_cv.tu_perfil_ha_sido_calificado', ['rating' => $result['rating']]))
                                            ->success()
                                            ->send();
                                    }) : null
                            ),
                    ]),

                Section::make(__('candidate.my_cv.resumen_profesional'))
                    ->description(fn () => Auth::user()->candidate->ai_rating ? __('candidate.my_cv.tu_perfil_tiene_una_calificacion', ['rating' => Auth::user()->candidate->ai_rating]) : __('candidate.my_cv.analiza_tu_cv_principal_para_obtener_una'))
                    ->schema([
                        Textarea::make('summary')
                            ->label(__('candidate.my_cv.sobre_mi'))
                            ->rows(3),
                        TextInput::make('phone_encrypted')
                            ->label(__('candidate.my_cv.telefono'))
                            ->tel(),
                    ]),

                Section::make(__('candidate.my_cv.experiencia_laboral'))
                    ->schema([
                        Repeater::make('work_experiences')
                            ->label('')
                            ->schema([
                                Hidden::make('id'),
                                TextInput::make('company_name')
                                    ->label(__('candidate.my_cv.empresa'))
                                    ->required(),
                                TextInput::make('job_title')
                                    ->label(__('candidate.my_cv.puesto'))
                                    ->required(),
                                DatePicker::make('start_date')
                                    ->label(__('candidate.my_cv.fecha_de_inicio')),
                                DatePicker::make('end_date')
                                    ->label(__('candidate.my_cv.fecha_de_fin')),
                            ])
                            ->columns(2),
                    ]),

                Section::make(__('candidate.my_cv.educacion'))
                    ->schema([
                        Repeater::make('educations')
                            ->label('')
                            ->schema([
                                Hidden::make('id'),
                                TextInput::make('institution')
                                    ->label(__('candidate.my_cv.institucion'))
                                    ->required(),
                                TextInput::make('degree')
                                    ->label(__('candidate.my_cv.grado_titulo'))
                                    ->required(),
                            ])
                            ->columns(2),
                    ]),

                Section::make(__('candidate.my_cv.habilidades'))
                    ->schema([
                        Repeater::make('skills')
                            ->label('')
                            ->schema([
                                Hidden::make('id'),
                                TextInput::make('name')
                                    ->label(__('candidate.my_cv.habilidad'))
                                    ->required(),
                                TextInput::make('level')
                                    ->label(__('candidate.my_cv.nivel_basico_intermedio_avanzado')),
                            ])
                            ->columns(2),
                    ]),

                Section::make(__('candidate.my_cv.documentos_adjuntos'))
                    ->description(__('candidate.my_cv.sube_tu_cv_certificados_u_otros_archivos'))
                    ->schema([
                        Repeater::make('documents')
                            ->hiddenLabel()
                            ->compact()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('candidate.my_cv.nombre_del_documento'))
                                    ->placeholder(__('candidate.my_cv.ej_certificado_de_ingles_cv_2024'))
                                    ->required(),
                                FileUpload::make('file_path')
                                    ->label(__('candidate.my_cv.archivo'))
                                    ->disk('local')
                                    ->directory('candidate-documents')
                                    ->visibility('private')
                                    ->acceptedFileTypes(CandidateDocument::ACCEPTED_FILE_TYPES)
                                    ->maxSize(5120) // 5MB
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $get) {
                                        if ($state instanceof TemporaryUploadedFile && $get('name')) {
                                            // The file is stored before form validation runs, so check it here.
                                            if (! in_array($state->getMimeType(), CandidateDocument::ACCEPTED_FILE_TYPES, true) || $state->getSize() > 5120 * 1024) {
                                                Notification::make()->title(__('candidate.my_cv.archivo_no_permitido'))->body(__('candidate.my_cv.sube_un_pdf_imagen_o_word_de_maximo_5mb'))->danger()->send();

                                                return;
                                            }

                                            $path = $state->store('candidate-documents', 'local');

                                            Auth::user()->candidate->documents()->updateOrCreate(
                                                ['file_path' => $path],
                                                ['name' => $get('name')]
                                            );
                                            Notification::make()->title(__('candidate.my_cv.documento_guardado_correctamente'))->success()->send();
                                        }
                                    })
                                    ->extraAttributes(['class' => 'mb-0'])
                                    ->hintAction(
                                        fn ($state, $record, $component) => $state ? Action::make('preview')
                                            ->label(__('candidate.my_cv.ver_archivo_actual'))
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
                ->label(__('candidate.my_cv.guardar_cambios'))
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
            ->title(__('candidate.my_cv.cv_actualizado_correctamente'))
            ->success()
            ->send();
    }
}
