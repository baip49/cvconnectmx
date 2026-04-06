<?php

namespace App\Enums;

enum UserType: string
{
    case Candidate = 'candidate';
    case Agency = 'agency';
    case Contractor = 'contractor';

    public function label(): string
    {
        return match ($this) {
            UserType::Candidate => 'Candidato',
            UserType::Agency => 'Agencia',
            UserType::Contractor => 'Contratista',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
        ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
        ->toArray();
    }
}
