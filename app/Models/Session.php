<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SessionType;
use App\Models\Concerns\BelongsToEvent;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Database\Factories\SessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A single item in the event schedule (talk, worship, meal, ...).
 *
 * @property int $id
 * @property int|null $room_id
 * @property string $title
 * @property string|null $description
 * @property CarbonImmutable $starts_at
 * @property CarbonImmutable $ends_at
 * @property SessionType $type
 * @property int $sort_order
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Room|null $room
 * @property-read Collection<int, Speaker> $speakers
 */
#[Table(name: 'event_sessions')]
#[Fillable([
    'room_id',
    'title',
    'description',
    'starts_at',
    'ends_at',
    'type',
    'sort_order',
])]
class Session extends Model
{
    use BelongsToEvent;

    /** @use HasFactory<SessionFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'sort_order' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
            'type' => SessionType::class,
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * @return BelongsToMany<Speaker, $this>
     */
    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class, 'session_speaker', 'session_id', 'speaker_id');
    }

    /**
     * @param  Builder<Session>  $query
     */
    #[Scope]
    protected function chronological(Builder $query): void
    {
        $query->orderBy('starts_at')->orderBy('sort_order')->orderBy('id');
    }

    public function isHappeningAt(CarbonInterface $moment): bool
    {
        return $moment->greaterThanOrEqualTo($this->starts_at) && $moment->lessThan($this->ends_at);
    }
}
