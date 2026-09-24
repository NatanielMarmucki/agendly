<?php

declare(strict_types=1);

namespace App\Services\Calendar;

use App\Models\Event;
use App\Models\Session;
use App\Models\Speaker;
use Illuminate\Support\Collection;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event as CalendarEvent;

/**
 * Builds .ics files. Times are written in the event's own timezone
 * (DTSTART;TZID=...) with a matching VTIMEZONE block, so calendars show the
 * right local time even across DST changes or when the phone is elsewhere.
 */
final class IcsCalendarBuilder
{
    /**
     * @param  Collection<int, Session>  $sessions
     */
    public function build(Event $event, Collection $sessions): string
    {
        $calendar = Calendar::create($event->name)
            ->productIdentifier('-//Agendly//'.config('app.name').'//PL')
            ->refreshInterval(60)
            ->source($event->publicUrl().'/calendar.ics');

        if ($event->description !== null) {
            $calendar->description($event->description);
        }

        foreach ($sessions as $session) {
            $calendar->event($this->toCalendarEvent($event, $session));
        }

        return $calendar->get();
    }

    private function toCalendarEvent(Event $event, Session $session): CalendarEvent
    {
        $calendarEvent = CalendarEvent::create($session->title)
            ->uniqueIdentifier(sprintf('session-%d@%s', $session->id, parse_url(config('app.url'), PHP_URL_HOST) ?: 'agendly'))
            ->startsAt($event->toLocal($session->starts_at))
            ->endsAt($event->toLocal($session->ends_at))
            ->url($event->publicUrl().'/sessions/'.$session->id);

        $description = collect([
            $session->description,
            $session->speakers->isEmpty() ? null : $session->speakers->map(fn (Speaker $speaker): string => $speaker->name)->join(', '),
        ])->filter()->join("\n\n");

        if ($description !== '') {
            $calendarEvent->description($description);
        }

        $location = collect([$session->room?->name, $event->venue])->filter()->join(', ');

        if ($location !== '') {
            $calendarEvent->address($location);
        }

        return $calendarEvent;
    }
}
