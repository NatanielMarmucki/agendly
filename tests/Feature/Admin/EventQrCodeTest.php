<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('the owner can download the QR code as PNG and SVG', function (): void {
    $event = Event::factory()->create();
    actingAs($event->user);

    $png = get(route('admin.events.qr', ['event' => $event, 'format' => 'png']));
    $png->assertOk()->assertHeader('Content-Type', 'image/png');
    expect(substr((string) $png->getContent(), 0, 8))->toBe("\x89PNG\r\n\x1a\n");

    $svg = get(route('admin.events.qr', ['event' => $event, 'format' => 'svg']));
    $svg->assertOk()->assertHeader('Content-Type', 'image/svg+xml');
    expect((string) $svg->getContent())->toContain('<svg');
});

test('other organizers cannot download the QR code', function (): void {
    $event = Event::factory()->create();
    actingAs(User::factory()->create());

    get(route('admin.events.qr', ['event' => $event, 'format' => 'png']))->assertForbidden();
});

test('guests are sent to the login page', function (): void {
    $event = Event::factory()->create();

    get(route('admin.events.qr', ['event' => $event, 'format' => 'svg']))->assertRedirect('/admin/login');
});
