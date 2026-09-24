<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Event;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Shared by every model that lives inside a single event.
 *
 * @property int $event_id
 * @property-read Event $event
 */
trait BelongsToEvent
{
    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
