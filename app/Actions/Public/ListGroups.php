<?php

declare(strict_types=1);

namespace App\Actions\Public;

use App\Data\Public\GroupData;
use App\Models\Event;
use App\Models\Group;

final class ListGroups
{
    /**
     * @return list<GroupData>
     */
    public function handle(Event $event): array
    {
        return array_values($event->groups()
            ->orderBy('name')
            ->get()
            ->map(fn (Group $group): GroupData => GroupData::fromModel($group))
            ->all());
    }
}
