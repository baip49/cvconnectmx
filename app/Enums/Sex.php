<?php

namespace App\Enums;

enum Sex: string
{
    case Male = 'M';
    case Female = 'F';

    public function label(): string
    {
        return match ($this) {
            Sex::Male => 'Masculino',
            Sex::Female => 'Femenino',
        };
        // (Se castea como $candidate->sex->label() :"Masculino/Femenino" o $candidate->sex->value :"M/F")
    }
}
