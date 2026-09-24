<?php

declare(strict_types=1);

namespace App\Data\Public;

use App\Enums\AnnouncementPriority;
use App\Models\Announcement;
use App\Models\Event;

final readonly class AnnouncementData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $body,
        public AnnouncementPriority $priority,
        public string $publishedAt,
    ) {}

    /**
     * Only call for published announcements.
     */
    public static function fromModel(Announcement $announcement, Event $event): self
    {
        return new self(
            id: $announcement->id,
            title: $announcement->title,
            body: $announcement->body,
            priority: $announcement->priority,
            publishedAt: $event->toLocal($announcement->published_at ?? now())->toIso8601String(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'priority' => $this->priority->value,
            'publishedAt' => $this->publishedAt,
        ];
    }
}
