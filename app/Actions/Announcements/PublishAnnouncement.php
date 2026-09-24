<?php

declare(strict_types=1);

namespace App\Actions\Announcements;

use App\Models\Announcement;

final class PublishAnnouncement
{
    public function handle(Announcement $announcement): Announcement
    {
        $announcement->forceFill(['published_at' => now()])->save();

        return $announcement;
    }
}
