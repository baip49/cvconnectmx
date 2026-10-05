<?php

namespace App\Filament\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class InitialsAvatarProvider implements AvatarProvider
{
    /**
     * @var list<string>
     */
    public const COLORS = ['0e9f6e', '1c64f2', 'c81e1e', '8e4ec6', 'd97706', '0e7490', '4d7c0f', 'a21caf'];

    public function get(Model|Authenticatable $record): string
    {
        $name = trim((string) ($record->name ?? 'Usuario'));

        $initials = method_exists($record, 'initials') && is_string($record->initials()) && $record->initials() !== ''
            ? $record->initials()
            : mb_strtoupper(mb_substr($name !== '' ? $name : '?', 0, 2));

        $background = self::COLORS[abs(crc32($name)) % count(self::COLORS)];

        $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64'>"
            ."<rect width='64' height='64' fill='#{$background}'/>"
            ."<text x='32' y='42' font-family='sans-serif' font-size='26' fill='#ffffff' text-anchor='middle'>"
            .htmlspecialchars($initials, ENT_QUOTES, 'UTF-8')
            .'</text></svg>';

        return 'data:image/svg+xml,'.rawurlencode($svg);
    }
}
