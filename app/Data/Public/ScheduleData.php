<?php

declare(strict_types=1);

namespace App\Data\Public;

final readonly class ScheduleData extends Data
{
    /**
     * @param  list<SessionData>  $sessions  Chronological.
     * @param  list<RoomData>  $rooms
     */
    public function __construct(
        public array $sessions,
        public array $rooms,
    ) {}

    public function toArray(): array
    {
        return [
            'sessions' => self::many($this->sessions),
            'rooms' => self::many($this->rooms),
        ];
    }
}
