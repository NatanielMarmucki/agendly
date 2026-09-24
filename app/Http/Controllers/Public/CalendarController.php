<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\EventCalendarRequest;
use App\Models\Event;
use App\Models\Session;
use App\Services\Calendar\IcsCalendarBuilder;
use Illuminate\Http\Response;

class CalendarController extends Controller
{
    public function __construct(private readonly IcsCalendarBuilder $builder) {}

    public function event(EventCalendarRequest $request, Event $event): Response
    {
        $ids = $request->sessionIds();

        $sessions = $event->sessions()
            ->with(['room', 'speakers'])
            ->when($ids !== null, fn ($query) => $query->whereKey($ids))
            ->chronological()
            ->get();

        return $this->download($this->builder->build($event, $sessions), $event->slug);
    }

    public function session(Event $event, Session $session): Response
    {
        $session->load(['room', 'speakers']);

        return $this->download(
            $this->builder->build($event, collect([$session])),
            "{$event->slug}-{$session->id}",
        );
    }

    private function download(string $ics, string $name): Response
    {
        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => sprintf('attachment; filename="%s.ics"', $name),
        ]);
    }
}
