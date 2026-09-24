<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Announcement;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when an announcement becomes visible to attendees.
 *
 * Extension point: nothing listens yet. Web Push delivery or a Laravel
 * Reverb broadcast (implement ShouldBroadcast on a listener or here) can
 * hook in without touching the admin panel or the public app.
 */
class AnnouncementPublished
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly Announcement $announcement) {}
}
