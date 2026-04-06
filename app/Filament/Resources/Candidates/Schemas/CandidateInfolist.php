<?php

namespace App\Filament\Resources\Candidates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CandidateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Name'),
                TextEntry::make('user.last_name')
                    ->label('Last Name'),
                TextEntry::make('user.email')
                    ->label('Email address'),
                TextEntry::make('phone'),
                TextEntry::make('age'),
                TextEntry::make('sex')
                    ->badge(),
                TextEntry::make('address'),
                TextEntry::make('scoring')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
