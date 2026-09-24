<?php

declare(strict_types=1);

namespace App\Data\Public;

use App\Models\Session;
use App\Models\Speaker;

final readonly class SpeakerData extends Data
{
    /**
     * @param  list<array{label: string, url: string}>  $links
     * @param  list<array{id: int, title: string}>  $sessions
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $bio,
        public ?string $photoUrl,
        public array $links,
        public array $sessions,
    ) {}

    /**
     * Expects `sessions` to be eager loaded.
     */
    public static function fromModel(Speaker $speaker): self
    {
        return new self(
            id: $speaker->id,
            name: $speaker->name,
            bio: $speaker->bio,
            photoUrl: $speaker->photoUrl(),
            links: array_values($speaker->links ?? []),
            sessions: $speaker->sessions
                ->sortBy('starts_at')
                ->map(fn (Session $session): array => ['id' => $session->id, 'title' => $session->title])
                ->values()
                ->all(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bio' => $this->bio,
            'photoUrl' => $this->photoUrl,
            'links' => $this->links,
            'sessions' => $this->sessions,
        ];
    }
}
