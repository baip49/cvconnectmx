<?php

namespace App\Enums;

enum UserType: string
{
    case Candidate = 'candidate';
    case Agency = 'agency';
    case Contractor = 'contractor';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            UserType::Candidate => 'Candidato',
            UserType::Agency => 'Agencia',
            UserType::Contractor => 'Contratista',
            UserType::Admin => 'Administrador',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->reject(fn ($case) => $case === self::Admin)
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
