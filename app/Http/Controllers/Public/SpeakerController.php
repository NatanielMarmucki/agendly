<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Actions\Public\ListSpeakers;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Inertia\Inertia;
use Inertia\Response;

class SpeakerController extends Controller
{
    public function __invoke(Event $event, ListSpeakers $speakers): Response
    {
        return Inertia::render('public/Speakers', [
            'speakers' => $speakers->handle($event),
        ]);
    }
}
