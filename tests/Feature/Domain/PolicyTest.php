<?php

declare(strict_types=1);

use App\Models\Announcement;
use App\Models\Event;
use App\Models\Group;
use App\Models\Room;
use App\Models\Session;
use App\Models\Speaker;
use App\Models\User;

test('organizers manage only their own events', function (): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $event = Event::factory()->for($owner)->create();

    expect($owner->can('update', $event))->toBeTrue()
        ->and($owner->can('delete', $event))->toBeTrue()
        ->and($other->can('view', $event))->toBeFalse()
        ->and($other->can('update', $event))->toBeFalse()
        ->and($other->can('delete', $event))->toBeFalse();
});

test('event content follows event ownership', function (string $model): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $record = $model::factory()->for(Event::factory()->for($owner))->create();

    expect($owner->can('update', $record))->toBeTrue()
        ->and($other->can('view', $record))->toBeFalse()
        ->and($other->can('update', $record))->toBeFalse()
        ->and($other->can('delete', $record))->toBeFalse();
})->with([Room::class, Session::class, Speaker::class, Announcement::class, Group::class]);
