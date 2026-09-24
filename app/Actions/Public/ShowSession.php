<?php

declare(strict_types=1);

namespace App\Actions\Public;

use App\Data\Public\SessionData;
use App\Data\Public\SpeakerData;
use App\Models\Event;
use App\Models\Session;
use App\Models\Speaker;

final class ShowSession
{
    /**
     * @return array{session: SessionData, speakers: list<SpeakerData>}
     */
    public function handle(Event $event, Session $session): array
    {
        $session->load(['room', 'speakers.sessions']);

        return [
            'session' => SessionData::fromModel($session, $event),
            'speakers' => array_values($session->speakers
                ->map(fn (Speaker $speaker): SpeakerData => SpeakerData::fromModel($speaker))
                ->all()),
        ];
    }
}
