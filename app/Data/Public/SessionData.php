<?php

declare(strict_types=1);

namespace App\Data\Public;

use App\Enums\SessionType;
use App\Models\Event;
use App\Models\Session;
use App\Models\Speaker;

/**
 * Times are ISO 8601 strings carrying the event's UTC offset, so the
 * client can both compare instants and display the event's wall-clock time.
 */
final readonly class SessionData extends Data
{
    /**
     * @param  list<SpeakerSummaryData>  $speakers
     */
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public SessionType $type,
        public bool $plannable,
        public string $startsAt,
        public string $endsAt,
        public string $day,
        public ?RoomData $room,
        public array $speakers,
    ) {}

    /**
     * Expects `room` and `speakers` to be eager loaded.
     */
    public static function fromModel(Session $session, Event $event): self
    {
        $startsAt = $event->toLocal($session->starts_at);

        return new self(
            id: $session->id,
            title: $session->title,
            description: $session->description,
            type: $session->type,
            plannable: $session->type->isPlannable(),
            startsAt: $startsAt->toIso8601String(),
            endsAt: $event->toLocal($session->ends_at)->toIso8601String(),
            day: $startsAt->toDateString(),
            room: $session->room === null ? null : RoomData::fromModel($session->room),
            speakers: $session->speakers
                ->map(fn (Speaker $speaker): SpeakerSummaryData => SpeakerSummaryData::fromModel($speaker))
                ->values()
                ->all(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type->value,
            'plannable' => $this->plannable,
            'startsAt' => $this->startsAt,
            'endsAt' => $this->endsAt,
            'day' => $this->day,
            'room' => $this->room?->toArray(),
            'speakers' => self::many($this->speakers),
        ];
    }
}
