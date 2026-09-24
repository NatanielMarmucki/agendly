<?php

declare(strict_types=1);

use App\Models\Announcement;
use App\Models\Event;
use App\Models\Group;
use App\Models\Room;
use App\Models\Session;
use App\Models\Speaker;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\get;

dataset('public pages', [
    'schedule' => ['', 'public/Schedule'],
    'plan' => ['/plan', 'public/Plan'],
    'announcements' => ['/announcements', 'public/Announcements'],
    'speakers' => ['/speakers', 'public/Speakers'],
    'groups' => ['/groups', 'public/Groups'],
    'info' => ['/info', 'public/Info'],
]);

test('published event pages render with shared event props', function (string $path, string $component): void {
    $event = Event::factory()->create(['name' => 'Konferencja']);

    get("/e/{$event->slug}{$path}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component($component)
            ->where('event.slug', $event->slug)
            ->where('event.name', 'Konferencja')
            ->where('event.timezone', 'Europe/Warsaw')
            ->where('locale', 'pl')
            ->has('translations'));
})->with('public pages');

test('unpublished events are not found', function (string $path): void {
    $event = Event::factory()->unpublished()->create();

    get("/e/{$event->slug}{$path}")->assertNotFound();
})->with('public pages');

test('unknown events are not found', function (): void {
    get('/e/does-not-exist')->assertNotFound();
});

test('the schedule lists sessions chronologically with times in the event timezone', function (): void {
    $event = Event::factory()->create(['timezone' => 'Europe/Warsaw']);
    $room = Room::factory()->for($event)->create(['name' => 'Namiot']);
    $speaker = Speaker::factory()->for($event)->create(['name' => 'Anna']);
    $later = Session::factory()->at($event, '2026-07-02 18:00')->create(['title' => 'Wieczór', 'room_id' => $room->id]);
    $earlier = Session::factory()->at($event, '2026-07-02 09:30', 45)->create(['title' => 'Poranek', 'type' => 'meal']);
    $later->speakers()->attach($speaker);

    get("/e/{$event->slug}")
        ->assertInertia(fn (Assert $page) => $page
            ->has('schedule.sessions', 2)
            ->where('schedule.sessions.0.id', $earlier->id)
            ->where('schedule.sessions.0.startsAt', '2026-07-02T09:30:00+02:00')
            ->where('schedule.sessions.0.endsAt', '2026-07-02T10:15:00+02:00')
            ->where('schedule.sessions.0.day', '2026-07-02')
            ->where('schedule.sessions.0.plannable', false)
            ->where('schedule.sessions.1.room.name', 'Namiot')
            ->where('schedule.sessions.1.speakers.0.name', 'Anna')
            ->has('schedule.rooms', 1));
});

test('session details are scoped to their event', function (): void {
    $event = Event::factory()->create();
    $session = Session::factory()->for($event)->create();
    $otherSession = Session::factory()->for(Event::factory())->create();

    get("/e/{$event->slug}/sessions/{$session->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/SessionShow')
            ->where('session.id', $session->id));

    get("/e/{$event->slug}/sessions/{$otherSession->id}")->assertNotFound();
});

test('the groups and speakers pages list the event data', function (): void {
    $event = Event::factory()->create();
    Group::factory()->for($event)->count(2)->create();
    Speaker::factory()->for($event)->create();
    Speaker::factory()->for(Event::factory())->create();

    get("/e/{$event->slug}/groups")->assertInertia(fn (Assert $page) => $page->has('groups', 2));
    get("/e/{$event->slug}/speakers")->assertInertia(fn (Assert $page) => $page->has('speakers', 1));
});

test('the latest important announcement is shared as a banner on every page', function (): void {
    $event = Event::factory()->create();
    Announcement::factory()->for($event)->important()->create(['title' => 'Stare', 'published_at' => now()->subHours(2)]);
    Announcement::factory()->for($event)->important()->create(['title' => 'Nowe', 'published_at' => now()->subHour()]);
    Announcement::factory()->for($event)->important()->draft()->create(['title' => 'Szkic']);

    get("/e/{$event->slug}/info")
        ->assertInertia(fn (Assert $page) => $page->where('banner.title', 'Nowe'));
});

test('public pages do not start a session or set tracking cookies', function (): void {
    $event = Event::factory()->create();

    $response = get("/e/{$event->slug}");

    expect($response->headers->getCookies())->toBeEmpty();
    $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('the language can be switched to English and is remembered', function (): void {
    $event = Event::factory()->create();

    get("/e/{$event->slug}?lang=en")
        ->assertCookie('agendly_locale', 'en', encrypted: false)
        ->assertInertia(fn (Assert $page) => $page
            ->where('locale', 'en')
            ->where('translations', fn ($translations): bool => $translations['public.nav.plan'] === 'My plan'));

    $this->withUnencryptedCookie('agendly_locale', 'en')
        ->get("/e/{$event->slug}")
        ->assertInertia(fn (Assert $page) => $page->where('locale', 'en'));
});

test('unsupported languages are ignored', function (): void {
    $event = Event::factory()->create();

    get("/e/{$event->slug}?lang=de")
        ->assertInertia(fn (Assert $page) => $page->where('locale', 'pl'));
});
