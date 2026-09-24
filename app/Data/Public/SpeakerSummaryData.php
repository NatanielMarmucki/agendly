<?php

declare(strict_types=1);

namespace App\Data\Public;

use App\Models\Speaker;

final readonly class SpeakerSummaryData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $photoUrl,
    ) {}

    public static function fromModel(Speaker $speaker): self
    {
        return new self($speaker->id, $speaker->name, $speaker->photoUrl());
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'photoUrl' => $this->photoUrl,
        ];
    }
}
