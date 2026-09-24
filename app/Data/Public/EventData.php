<?php

declare(strict_types=1);

namespace App\Data\Public;

use App\Models\Event;
use Carbon\CarbonImmutable;

final readonly class EventData extends Data
{
    /**
     * @param  list<string>  $days  Local calendar days (Y-m-d) in the event timezone.
     */
    public function __construct(
        public string $slug,
        public string $name,
        public ?string $description,
        public ?string $venue,
        public string $timezone,
        public string $startsAt,
        public string $endsAt,
        public array $days,
        public ?string $coverImageUrl,
    ) {}

    public static function fromModel(Event $event): self
    {
        return new self(
            slug: $event->slug,
            name: $event->name,
            description: $event->description,
            venue: $event->venue,
            timezone: $event->timezone,
            startsAt: $event->toLocal($event->starts_at)->toIso8601String(),
            endsAt: $event->toLocal($event->ends_at)->toIso8601String(),
            days: array_map(fn (CarbonImmutable $day): string => $day->toDateString(), $event->days()),
            coverImageUrl: $event->coverImageUrl(),
        );
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'venue' => $this->venue,
            'timezone' => $this->timezone,
            'startsAt' => $this->startsAt,
            'endsAt' => $this->endsAt,
            'days' => $this->days,
            'coverImageUrl' => $this->coverImageUrl,
        ];
    }
}
