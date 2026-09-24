<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Actions\Public\BuildAnnouncementFeed;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function index(Event $event, BuildAnnouncementFeed $feed): Response
    {
        return Inertia::render('public/Announcements', [
            'feed' => $feed->handle($event),
        ]);
    }

    /**
     * Polled by the client every 60 s (see useAnnouncements.ts).
     */
    public function feed(Event $event, BuildAnnouncementFeed $feed): JsonResponse
    {
        return response()
            ->json($feed->handle($event))
            ->header('Cache-Control', 'no-cache');
    }
}
