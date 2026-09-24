<?php

declare(strict_types=1);

namespace App\Actions\Public;

use App\Data\Public\AnnouncementData;
use App\Data\Public\AnnouncementFeedData;
use App\Enums\AnnouncementPriority;
use App\Models\Announcement;
use App\Models\Event;

final class BuildAnnouncementFeed
{
    public function handle(Event $event): AnnouncementFeedData
    {
        $announcements = $event->announcements()
            ->published()
            ->latestFirst()
            ->get();

        $banner = $announcements->first(
            fn (Announcement $announcement): bool => $announcement->priority === AnnouncementPriority::Important,
        );

        return new AnnouncementFeedData(
            announcements: $announcements
                ->map(fn (Announcement $announcement): AnnouncementData => AnnouncementData::fromModel($announcement, $event))
                ->values()
                ->all(),
            banner: $banner === null ? null : AnnouncementData::fromModel($banner, $event),
        );
    }

    /**
     * Only the latest important announcement – shared on every page.
     */
    public function banner(Event $event): ?AnnouncementData
    {
        $banner = $event->announcements()
            ->published()
            ->where('priority', AnnouncementPriority::Important)
            ->latestFirst()
            ->first();

        return $banner === null ? null : AnnouncementData::fromModel($banner, $event);
    }
}
