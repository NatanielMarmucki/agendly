<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Announcement;
use App\Models\Group;
use App\Models\Room;
use App\Models\Session;
use App\Models\Speaker;
use App\Models\User;

/**
 * Shared rules for everything that belongs to an event (rooms, sessions,
 * speakers, announcements, groups): access follows event ownership.
 *
 * Class-level abilities (viewAny, create, deleteAny) are always granted
 * because these records are only ever listed and created through an event
 * the organizer already owns (Filament relation managers).
 */
abstract class EventContentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Room|Session|Speaker|Announcement|Group $model): bool
    {
        return $model->event->isOwnedBy($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Room|Session|Speaker|Announcement|Group $model): bool
    {
        return $model->event->isOwnedBy($user);
    }

    public function delete(User $user, Room|Session|Speaker|Announcement|Group $model): bool
    {
        return $model->event->isOwnedBy($user);
    }

    public function deleteAny(User $user): bool
    {
        return true;
    }
}
