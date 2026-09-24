<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Actions\Public\BuildSchedule;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Inertia\Inertia;
use Inertia\Response;

/**
 * "My plan" lives in the browser's localStorage only; the page receives the
 * whole schedule and filters it client-side, so nothing is sent back.
 */
class PlanController extends Controller
{
    public function __invoke(Event $event, BuildSchedule $schedule): Response
    {
        return Inertia::render('public/Plan', [
            'schedule' => $schedule->handle($event),
        ]);
    }
}
