<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AnnouncementPriority: string implements HasColor, HasLabel
{
    case Normal = 'normal';
    case Important = 'important';

    public function getLabel(): string
    {
        return __('enums.announcement_priority.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Normal => 'gray',
            self::Important => 'danger',
        };
    }
}
