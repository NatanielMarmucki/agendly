<?php

declare(strict_types=1);

use App\Models\Announcement;
use App\Models\Event;

use function Pest\Laravel\getJson;

test('the feed returns published announcements newest first', function (): void {
    $event = Event::factory()->create();
    $older = Announcement::factory()->for($event)->create(['published_at' => now()->subHours(3)]);
    $newer = Announcement::factory()->for($event)->create(['published_at' => now()->subHour()]);
    Announcement::factory()->for($event)->draft()->create();
    Announcement::factory()->for($event)->scheduled()->create();
    Announcement::factory()->for(Event::factory())->create();

    getJson("/e/{$event->slug}/announcements.json")
        ->assertOk()
        ->assertHeader('Cache-Control', 'no-cache, private')
        ->assertJsonCount(2, 'announcements')
        ->assertJsonPath('announcements.0.id', $newer->id)
        ->assertJsonPath('announcements.1.id', $older->id)
        ->assertJsonPath('banner', null)
        ->assertJsonStructure([
            'announcements' => [['id', 'title', 'body', 'priority', 'publishedAt']],
            'banner',
        ]);
});

test('the feed exposes the latest important announcement as the banner', function (): void {
    $event = Event::factory()->create();
    $important = Announcement::factory()->for($event)->important()->create([
        'title' => 'Zmiana sali',
        'published_at' => now()->subMinutes(30),
    ]);
    Announcement::factory()->for($event)->create(['published_at' => now()->subMinutes(5)]);

    getJson("/e/{$event->slug}/announcements.json")
        ->assertJsonPath('banner.id', $important->id)
        ->assertJsonPath('banner.priority', 'important')
        ->assertJsonPath('banner.title', 'Zmiana sali');
});

test('the feed formats publication time in the event timezone', function (): void {
    $this->travelTo(now()->setDate(2026, 1, 15)->setTime(12, 0));
    $event = Event::factory()->create(['timezone' => 'Europe/Warsaw']);
    Announcement::factory()->for($event)->create(['published_at' => now()->utc()->setTime(10, 0)]);

    getJson("/e/{$event->slug}/announcements.json")
        ->assertJsonPath('announcements.0.publishedAt', '2026-01-15T11:00:00+01:00');
});

test('the feed is not available for unpublished events', function (): void {
    $event = Event::factory()->unpublished()->create();

    getJson("/e/{$event->slug}/announcements.json")->assertNotFound();
});
