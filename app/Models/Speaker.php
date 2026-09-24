<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToEvent;
use Carbon\CarbonImmutable;
use Database\Factories\SpeakerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $name
 * @property string|null $bio
 * @property string|null $photo_path
 * @property list<array{label: string, url: string}>|null $links
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, Session> $sessions
 */
#[Fillable(['name', 'bio', 'photo_path', 'links'])]
class Speaker extends Model
{
    use BelongsToEvent;

    /** @use HasFactory<SpeakerFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'links' => 'array',
        ];
    }

    /**
     * @return BelongsToMany<Session, $this>
     */
    public function sessions(): BelongsToMany
    {
        return $this->belongsToMany(Session::class, 'session_speaker', 'speaker_id', 'session_id');
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path === null
            ? null
            : Storage::disk('public')->url($this->photo_path);
    }
}
