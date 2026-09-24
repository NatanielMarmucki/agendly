<?php

declare(strict_types=1);

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Events\RelationManagers\AnnouncementsRelationManager;
use App\Filament\Resources\Events\RelationManagers\SessionsRelationManager;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Room;
use App\Models\Session;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->organizer = User::factory()->create();
    $this->otherOrganizer = User::factory()->create();
    $this->event = Event::factory()->for($this->organizer)->create();
    $this->foreignEvent = Event::factory()->for($this->otherOrganizer)->create();
});

test('guests are redirected to the Filament login', function (): void {
    get('/admin/events')->assertRedirect('/admin/login');
});

test('organizers only see their own events', function (): void {
    actingAs($this->organizer);

    Livewire::test(ListEvents::class)
        ->assertCanSeeTableRecords([$this->event])
        ->assertCanNotSeeTableRecords([$this->foreignEvent]);
});

test('organizers cannot open another organizer\'s event', function (): void {
    actingAs($this->organizer);

    get("/admin/events/{$this->event->id}/edit")->assertOk();
    get("/admin/events/{$this->foreignEvent->id}/edit")->assertNotFound();
});

test('admin pages render', function (): void {
    actingAs($this->organizer);

    get('/admin')->assertOk();
    get('/admin/events')->assertOk();
    get('/admin/events/create')->assertOk();
});

test('creating an event assigns it to the signed-in organizer', function (): void {
    actingAs($this->organizer);

    Livewire::test(CreateEvent::class)
        ->fillForm([
            'name' => 'Obóz letni',
            'slug' => 'oboz-letni',
            'timezone' => 'Europe/Warsaw',
            'starts_at' => '2026-07-01 09:00:00',
            'ends_at' => '2026-07-05 12:00:00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $event = Event::query()->where('slug', 'oboz-letni')->firstOrFail();

    expect($event->user_id)->toBe($this->organizer->id)
        // Entered as Warsaw wall-clock time (UTC+2 in July), stored as UTC.
        ->and($event->starts_at->utc()->format('Y-m-d H:i'))->toBe('2026-07-01 07:00');
});

test('event end must be after its start', function (): void {
    actingAs($this->organizer);

    Livewire::test(CreateEvent::class)
        ->fillForm([
            'name' => 'Zły termin',
            'slug' => 'zly-termin',
            'timezone' => 'Europe/Warsaw',
            'starts_at' => '2026-07-05 09:00:00',
            'ends_at' => '2026-07-01 12:00:00',
        ])
        ->call('create')
        ->assertHasFormErrors(['ends_at']);
});

test('sessions relation manager lists only the event\'s sessions', function (): void {
    actingAs($this->organizer);
    $own = Session::factory()->for($this->event)->create();
    $foreign = Session::factory()->for($this->foreignEvent)->create();

    Livewire::test(SessionsRelationManager::class, [
        'ownerRecord' => $this->event,
        'pageClass' => EditEvent::class,
    ])
        ->assertOk()
        ->assertCanSeeTableRecords([$own])
        ->assertCanNotSeeTableRecords([$foreign]);
});

test('a session cannot be assigned to another event\'s room', function (): void {
    actingAs($this->organizer);
    $foreignRoom = Room::factory()->for($this->foreignEvent)->create();

    Livewire::test(SessionsRelationManager::class, [
        'ownerRecord' => $this->event,
        'pageClass' => EditEvent::class,
    ])
        ->callTableAction('create', data: [
            'title' => 'Wykład',
            'type' => 'talk',
            'room_id' => $foreignRoom->id,
            'starts_at' => '2026-07-01 10:00:00',
            'ends_at' => '2026-07-01 11:00:00',
        ])
        ->assertHasTableActionErrors(['room_id']);
});

test('publish now makes a draft announcement visible', function (): void {
    actingAs($this->organizer);
    $draft = Announcement::factory()->for($this->event)->draft()->create();

    Livewire::test(AnnouncementsRelationManager::class, [
        'ownerRecord' => $this->event,
        'pageClass' => EditEvent::class,
    ])->callTableAction('publishNow', $draft);

    expect($draft->refresh()->isPublished())->toBeTrue();
});
