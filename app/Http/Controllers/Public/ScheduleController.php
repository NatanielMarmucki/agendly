<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Actions\Public\BuildSchedule;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    public function __invoke(Event $event, BuildSchedule $schedule): Response
    {
        return Inertia::render('public/Schedule', [
            'schedule' => $schedule->handle($event),
        ]);
    }
}
