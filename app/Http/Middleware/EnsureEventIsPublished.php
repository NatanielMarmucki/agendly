<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unpublished (or unknown) events do not exist for attendees.
 */
class EnsureEventIsPublished
{
    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route('event');

        abort_unless($event instanceof Event && $event->is_published, 404);

        return $next($request);
    }
}
