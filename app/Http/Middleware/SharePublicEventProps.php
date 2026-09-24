<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Actions\Public\BuildAnnouncementFeed;
use App\Data\Public\EventData;
use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Props every public event page needs: the event itself and the latest
 * important announcement (rendered as a banner by the layout).
 */
class SharePublicEventProps
{
    public function __construct(private readonly BuildAnnouncementFeed $feed) {}

    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route('event');

        if ($event instanceof Event) {
            Inertia::share([
                'event' => fn (): EventData => EventData::fromModel($event),
                'banner' => fn () => $this->feed->banner($event),
            ]);
        }

        return $next($request);
    }
}
