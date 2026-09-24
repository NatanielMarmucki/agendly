<?php

declare(strict_types=1);

use App\Enums\SessionType;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Session;
use Carbon\CarbonImmutable;

test('event days are computed in the event timezone', function (): void {
    // 23:30 UTC on the 1st is already the 2nd in Warsaw (UTC+2 in summer).
    $event = Event::factory()->create([
        'starts_at' => CarbonImmutable::parse('2026-07-01 23:30', 'UTC'),
        'ends_at' => CarbonImmutable::parse('2026-07-03 10:00', 'UTC'),
        'timezone' => 'Europe/Warsaw',
    ]);

    expect(array_map(fn (CarbonImmutable $day): string => $day->toDateString(), $event->days()))
        ->toBe(['2026-07-02', '2026-07-03']);
});

test('new events default to the Warsaw timezone and are unpublished', function (): void {
    $event = new Event;

    expect($event->timezone)->toBe('Europe/Warsaw')
        ->and($event->is_published)->toBeFalse();
});

test('session casts its type to an enum', function (): void {
    $session = Session::factory()->create(['type' => 'worship']);

    expect($session->refresh()->type)->toBe(SessionType::Worship);
});

test('only past, non-draft announcements are published', function (): void {
    $event = Event::factory()->create();
    $visible = Announcement::factory()->for($event)->create();
    Announcement::factory()->for($event)->draft()->create();
    Announcement::factory()->for($event)->scheduled()->create();

    expect($event->announcements()->published()->pluck('id')->all())->toBe([$visible->id]);
});
