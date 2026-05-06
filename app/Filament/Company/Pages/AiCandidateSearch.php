<?php

namespace App\Filament\Company\Pages;

use App\Models\Candidate;
use App\Models\Application;
use App\Models\Vacancy;
use App\Services\AiService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\Action;
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

    protected static ?string $title = 'Búsqueda de candidatos';

    protected static ?string $navigationLabel = 'Búsqueda de candidatos';

    protected string $view = 'filament.company.pages.ai-candidate-search';

    public ?string $searchQuery = '';
    public array $aiFilters = [];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('searchQuery')
                    ->label('¿Qué perfil estás buscando?')
                    ->placeholder('Ej: Desarrollador Fullstack con experiencia en Laravel y React...')
                    ->hint('Describe las habilidades y experiencia ideal.')
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
            ->title('Búsqueda procesada por IA')
            ->success()
            ->send();
            
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Candidate::query()
                    ->when(isset($this->aiFilters['skills']), function (Builder $query) {
                        foreach ($this->aiFilters['skills'] as $skill) {
                            $query->whereHas('skills', fn($q) => $q->where('name', 'like', "%{$skill}%"));
                        }
                    })
                    ->when(isset($this->aiFilters['keywords']), function (Builder $query) {
                        $query->where(function ($q) {
                            foreach ($this->aiFilters['keywords'] as $keyword) {
                                $q->orWhere('summary', 'like', "%{$keyword}%")
                                  ->orWhere('professional_title', 'like', "%{$keyword}%");
                            }
                        });
                    })
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('professional_title')
                    ->label('Título')
                    ->limit(30),
                TextColumn::make('ai_rating')
                    ->label('Rating IA')
                    ->badge()
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->sortable(),
                TextColumn::make('skills.name')
                    ->label('Habilidades')
                    ->badge()
                    ->limitList(3),
            ])
            ->actions([
                Action::make('offer_vacancy')
                    ->label('Ofrecer Vacante')
                    ->icon('heroicon-o-briefcase')
                    ->form([
                        Select::make('vacancy_id')
                            ->label('Seleccionar Vacante')
                            ->options(fn() => Vacancy::where('company_id', Auth::user()->company->id)->pluck('title', 'id'))
                            ->required(),
                    ])
                    ->action(function (Candidate $record, array $data) {
                        Application::create([
                            'candidate_id' => $record->id,
                            'vacancy_id' => $data['vacancy_id'],
                            'status' => 'offered',
                            'is_offer' => true,
                        ]);

                        Notification::make()
                            ->title('Oferta enviada')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
