<?php

declare(strict_types=1);

namespace App\Actions\Public;

use App\Data\Public\RoomData;
use App\Data\Public\ScheduleData;
use App\Data\Public\SessionData;
use App\Models\Event;
use App\Models\Room;
use App\Models\Session;

final class BuildSchedule
{
    public function handle(Event $event): ScheduleData
    {
        $sessions = array_values($event->sessions()
            ->with(['room', 'speakers'])
            ->chronological()
            ->get()
            ->map(fn (Session $session): SessionData => SessionData::fromModel($session, $event))
            ->all());

        $rooms = array_values($event->rooms()
            ->orderBy('name')
            ->get()
            ->map(fn (Room $room): RoomData => RoomData::fromModel($room))
            ->all());

        return new ScheduleData($sessions, $rooms);
    }
}
