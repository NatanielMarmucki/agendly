<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Room;
use App\Models\Session;
use App\Models\Speaker;

use function Pest\Laravel\get;

/**
 * Unfold RFC 5545 folded lines so assertions can match whole properties.
 */
function unfoldIcs(string $ics): string
{
    return str_replace(["\r\n ", "\r\n"], ['', "\n"], $ics);
}

test('a single session exports with the event timezone', function (): void {
    $event = Event::factory()->create(['timezone' => 'Europe/Warsaw', 'venue' => 'Wisła']);
    $room = Room::factory()->for($event)->create(['name' => 'Namiot']);
    $session = Session::factory()
        ->at($event, '2026-07-02 19:00', 75)
        ->create(['title' => 'Tożsamość', 'room_id' => $room->id]);
    $session->speakers()->attach(Speaker::factory()->for($event)->create(['name' => 'Anna Kowalczyk']));

    $response = get("/e/{$event->slug}/sessions/{$session->id}.ics");

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/calendar; charset=utf-8')
        ->assertHeader('Content-Disposition', "attachment; filename=\"{$event->slug}-{$session->id}.ics\"");

    $ics = unfoldIcs((string) $response->getContent());

    expect($ics)
        ->toContain('BEGIN:VCALENDAR')
        ->toContain('TZID:Europe/Warsaw')
        // 19:00 Warsaw wall-clock time, not the stored 17:00 UTC.
        ->toContain('DTSTART;TZID=Europe/Warsaw:20260702T190000')
        ->toContain('DTEND;TZID=Europe/Warsaw:20260702T201500')
        ->toContain('SUMMARY:Tożsamość')
        ->toContain('LOCATION:Namiot\, Wisła')
        ->toContain('Anna Kowalczyk')
        ->toContain("UID:session-{$session->id}@")
        ->and(substr_count($ics, 'BEGIN:VEVENT'))->toBe(1);
});

test('winter sessions use the standard-time offset', function (): void {
    $event = Event::factory()->create(['timezone' => 'Europe/Warsaw']);
    $session = Session::factory()->at($event, '2027-01-26 09:00')->create();

    $ics = unfoldIcs((string) get("/e/{$event->slug}/sessions/{$session->id}.ics")->getContent());

    expect($ics)->toContain('DTSTART;TZID=Europe/Warsaw:20270126T090000');
    expect($session->starts_at->utc()->format('H:i'))->toBe('08:00');
});

test('the whole event exports every session', function (): void {
    $event = Event::factory()->create();
    Session::factory()->for($event)->count(3)->create();
    Session::factory()->for(Event::factory())->create();

    $response = get("/e/{$event->slug}/calendar.ics");

    $response->assertOk()->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
    expect(substr_count((string) $response->getContent(), 'BEGIN:VEVENT'))->toBe(3);
});

test('the export can be limited to the sessions in my plan', function (): void {
    $event = Event::factory()->create();
    [$a, $b] = Session::factory()->for($event)->count(3)->create();
    $foreign = Session::factory()->for(Event::factory())->create();

    $ics = (string) get("/e/{$event->slug}/calendar.ics?sessions={$a->id},{$b->id},{$foreign->id}")->getContent();

    expect(substr_count($ics, 'BEGIN:VEVENT'))->toBe(2)
        ->and($ics)->toContain("session-{$a->id}@")
        ->and($ics)->not->toContain("session-{$foreign->id}@");
});

test('invalid session filters return a validation error', function (): void {
    $event = Event::factory()->create();

    get("/e/{$event->slug}/calendar.ics?sessions=abc")
        ->assertUnprocessable()
        ->assertJsonValidationErrors('sessions.0');
});

test('calendars are not available for unpublished events or foreign sessions', function (): void {
    $unpublished = Event::factory()->unpublished()->create();
    $published = Event::factory()->create();
    $foreign = Session::factory()->for(Event::factory())->create();

    get("/e/{$unpublished->slug}/calendar.ics")->assertNotFound();
    get("/e/{$published->slug}/sessions/{$foreign->id}.ics")->assertNotFound();
});
