<?php

declare(strict_types=1);

namespace App\Filament\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Inline SVG initials instead of Filament's default ui-avatars.com images:
 * no third-party requests, works offline and on locked-down networks.
 */
class InitialsAvatarProvider implements AvatarProvider
{
    public function get(Model $record): string
    {
        $initials = Str::of(Filament::getNameForDefaultAvatar($record))
            ->trim()
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->join('');

        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" fill="#4f46e5"/>'
            .'<text x="50%%" y="50%%" dy=".35em" text-anchor="middle" font-family="system-ui,sans-serif" font-size="26" fill="#fff">%s</text></svg>',
            e($initials),
        );

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
