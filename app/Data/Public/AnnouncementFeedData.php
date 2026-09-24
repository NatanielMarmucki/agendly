<?php

declare(strict_types=1);

namespace App\Data\Public;

/**
 * Payload of the announcements JSON endpoint polled by the public app.
 */
final readonly class AnnouncementFeedData extends Data
{
    /**
     * @param  list<AnnouncementData>  $announcements  Newest first.
     */
    public function __construct(
        public array $announcements,
        public ?AnnouncementData $banner,
    ) {}

    public function toArray(): array
    {
        return [
            'announcements' => self::many($this->announcements),
            'banner' => $this->banner?->toArray(),
        ];
    }
}
