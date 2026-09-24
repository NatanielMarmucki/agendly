<?php

declare(strict_types=1);

namespace App\Actions\Public;

use App\Data\Public\SpeakerData;
use App\Models\Event;
use App\Models\Speaker;

final class ListSpeakers
{
    /**
     * @return list<SpeakerData>
     */
    public function handle(Event $event): array
    {
        return array_values($event->speakers()
            ->with('sessions')
            ->orderBy('name')
            ->get()
            ->map(fn (Speaker $speaker): SpeakerData => SpeakerData::fromModel($speaker))
            ->all());
    }
}
