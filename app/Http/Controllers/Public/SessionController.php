<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Actions\Public\ShowSession;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Session;
use Inertia\Inertia;
use Inertia\Response;

class SessionController extends Controller
{
    public function __invoke(Event $event, Session $session, ShowSession $show): Response
    {
        return Inertia::render('public/SessionShow', $show->handle($event, $session));
    }
}
