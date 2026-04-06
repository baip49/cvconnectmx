<?php

namespace App\Filament\Resources\Candidates\Schemas;

use App\Enums\Sex;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CandidateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Datos del Usuario (se actualizan en User, no en Candidate)
                Section::make([
                    TextInput::make('user.name')
                        ->label('Name')
                        ->required()
                        ->dehydrated(false), // No se guarda directo en Candidate
                    TextInput::make('user.last_name')
                        ->label('Last Name')
                        ->required()
                        ->dehydrated(false),
                    TextInput::make('user.email')
                        ->label('Email address')
                        ->email()
                        ->required()
                        ->dehydrated(false),
                ])
                    ->label('User Information')
                    ->columns(2),

                // Datos del Candidato
                Section::make([
                    TextInput::make('phone')
                        ->tel()
                        ->required(),
                    TextInput::make('age')
                        ->numeric()
                        ->required(),
                    Select::make('sex')
                        ->options(Sex::class)
                        ->required(),
                    TextInput::make('address')
                        ->required(),
                    TextInput::make('scoring')
                        ->numeric()
                        ->default(0)
                        ->required(),
                ])
                    ->label('Candidate Information')
                    ->columns(2),
            ]);
    }
}
