<?php

declare(strict_types=1);

namespace App\Data\Public;

use App\Models\Room;

final readonly class RoomData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
    ) {}

    public static function fromModel(Room $room): self
    {
        return new self($room->id, $room->name, $room->description);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
