<?php

declare(strict_types=1);

use function Pest\Laravel\get;

test('the service worker is served from the site root when built', function (): void {
    if (! is_file(public_path('build/sw.js'))) {
        get('/sw.js')->assertNotFound();

        return;
    }

    get('/sw.js')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/javascript; charset=utf-8')
        ->assertHeader('Service-Worker-Allowed', '/');
});

test('the web app manifest is published', function (): void {
    $manifest = json_decode((string) file_get_contents(public_path('manifest.webmanifest')), true);

    expect($manifest)
        ->name->toBe('Agendly')
        ->start_url->toBe('/')
        ->display->toBe('standalone')
        ->and($manifest['icons'])->toHaveCount(3);
});
