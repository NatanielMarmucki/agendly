<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Actions\Public\ListGroups;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{
    public function __invoke(Event $event, ListGroups $groups): Response
    {
        return Inertia::render('public/Groups', [
            'groups' => $groups->handle($event),
        ]);
    }
}
