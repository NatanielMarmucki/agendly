<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToEvent;
use Carbon\CarbonImmutable;
use Database\Factories\GroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A small group / workshop track attendees are assigned to offline.
 *
 * @property int $id
 * @property string $name
 * @property string|null $leader_name
 * @property string|null $location
 * @property string|null $description
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['name', 'leader_name', 'location', 'description'])]
class Group extends Model
{
    use BelongsToEvent;

    /** @use HasFactory<GroupFactory> */
    use HasFactory;
}
