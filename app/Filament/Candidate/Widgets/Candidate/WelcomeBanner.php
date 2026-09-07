<?php

namespace App\Filament\Candidate\Widgets\Candidate;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeBanner extends Widget
{
    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.candidate.widgets.candidate.welcome-banner';

    public function getViewData(): array
    {
        $candidate = Auth::user()?->candidate;

        return [
            'name' => Auth::user()?->name ?? 'Candidato',
            'rating' => $candidate?->ai_rating,
            'isBlocked' => $candidate?->is_blocked ?? false,
        ];
    }
}
