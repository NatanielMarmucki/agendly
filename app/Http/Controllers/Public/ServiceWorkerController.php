<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Serves the Vite-built service worker from the site root so its scope
 * covers every event page (a worker under /build/ could only control /build/).
 */
class ServiceWorkerController extends Controller
{
    public function __invoke(): Response
    {
        $path = public_path('build/sw.js');

        abort_unless(is_file($path), 404);

        return response((string) file_get_contents($path), 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Cache-Control' => 'no-cache',
            'Service-Worker-Allowed' => '/',
        ]);
    }
}
