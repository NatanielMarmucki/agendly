<?php

declare(strict_types=1);

namespace App\Data\Public;

use App\Models\Group;

final readonly class GroupData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $leaderName,
        public ?string $location,
        public ?string $description,
    ) {}

    public static function fromModel(Group $group): self
    {
        return new self($group->id, $group->name, $group->leader_name, $group->location, $group->description);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'leaderName' => $this->leaderName,
            'location' => $this->location,
            'description' => $this->description,
        ];
    }
}
