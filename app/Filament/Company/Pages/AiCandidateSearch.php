<?php

namespace App\Filament\Company\Pages;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Services\AiService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AiCandidateSearch extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    public function getTitle(): string
    {
        return __('company.ai_candidate_search.busqueda_de_candidatos');
    }

    public static function getNavigationLabel(): string
    {
        return __('company.ai_candidate_search.busqueda_de_candidatos');
    }

    protected string $view = 'filament.company.pages.ai-candidate-search';

    public ?string $searchQuery = '';

    public array $aiFilters = [];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('searchQuery')
                    ->label(__('company.ai_candidate_search.que_perfil_estas_buscando'))
                    ->placeholder(__('company.ai_candidate_search.ej_desarrollador_fullstack_con_experienc'))
                    ->hint(__('company.ai_candidate_search.describe_las_habilidades_y_experiencia_i'))
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function runAiSearch(AiService $aiService)
    {
        if (empty($this->searchQuery)) {
            return;
        }

        $this->aiFilters = $aiService->searchCandidates($this->searchQuery);

        Notification::make()
            ->title(__('company.ai_candidate_search.busqueda_procesada_por_ia'))
            ->success()
            ->send();

        $this->resetTable();
    }

    public function resetAiSearch(): void
    {
        $this->searchQuery = '';
        $this->aiFilters = [];

        $this->resetTable();

        Notification::make()
            ->title(__('company.ai_candidate_search.filtros_reiniciados'))
            ->body(__('company.ai_candidate_search.mostrando_todos_los_candidatos_ordenados'))
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Candidate::query()
                    ->orderByRaw('ai_rating IS NULL, ai_rating DESC')
                    ->where(function (Builder $query) {
                        $query->when(! empty($this->aiFilters['skills']), function (Builder $q) {
                            $q->whereHas('skills', function ($skillQuery) {
                                $skillQuery->whereIn('name', $this->aiFilters['skills']);
                                // O búsqueda parcial si no son nombres exactos
                                foreach ($this->aiFilters['skills'] as $skill) {
                                    $skillQuery->orWhere('name', 'like', "%{$skill}%");
                                }
                            });
                        });

                        $query->when(! empty($this->aiFilters['keywords']), function (Builder $q) {
                            $q->orWhere(function ($keywordGroup) {
                                foreach ($this->aiFilters['keywords'] as $keyword) {
                                    $keywordGroup
                                        ->orWhere('summary', 'like', "%{$keyword}%")
                                        ->orWhere('professional_title', 'like', "%{$keyword}%")
                                        ->orWhereHas('skills', fn ($skillQuery) => $skillQuery->where('name', 'like', "%{$keyword}%"));
                                }
                            });
                        });
                    })
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('company.ai_candidate_search.nombre'))
                    ->searchable(),
                TextColumn::make('professional_title')
                    ->label(__('company.ai_candidate_search.titulo'))
                    ->limit(30),
                TextColumn::make('ai_rating')
                    ->label(__('company.ai_candidate_search.rating_ia'))
                    ->badge()
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->sortable(),
                TextColumn::make('skills.name')
                    ->label(__('company.ai_candidate_search.habilidades'))
                    ->badge()
                    ->limitList(3),
            ])
            ->actions([
                Action::make('offer_vacancy')
                    ->label(__('company.ai_candidate_search.ofrecer_vacante'))
                    ->icon('heroicon-o-briefcase')
                    ->form([
                        Select::make('vacancy_id')
                            ->label(__('company.ai_candidate_search.seleccionar_vacante'))
                            ->options(fn () => Vacancy::where('company_id', Auth::user()->company->id)->pluck('title', 'id'))
                            ->required(),
                    ])
                    ->action(function (Candidate $record, array $data) {
                        Application::updateOrCreate(
                            ['candidate_id' => $record->id, 'vacancy_id' => $data['vacancy_id']],
                            ['status' => 'offered', 'is_offer' => true],
                        );

                        Notification::make()
                            ->title(__('company.ai_candidate_search.oferta_enviada'))
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
