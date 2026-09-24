<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SessionType: string implements HasColor, HasLabel
{
    case Talk = 'talk';
    case Worship = 'worship';
    case Workshop = 'workshop';
    case Meal = 'meal';
    case Break = 'break';
    case Other = 'other';

    public function getLabel(): string
    {
        return __('enums.session_type.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Talk => 'primary',
            self::Worship => 'warning',
            self::Workshop => 'success',
            self::Meal, self::Break => 'gray',
            self::Other => 'info',
        };
    }

    /**
     * Breaks and meals are part of the day but not something attendees "attend".
     */
    public function isPlannable(): bool
    {
        return ! in_array($this, [self::Meal, self::Break], true);
    }
}
