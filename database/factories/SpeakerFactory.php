<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use App\Models\Speaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Speaker>
 */
class SpeakerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => fake()->name(),
            'bio' => fake()->paragraph(),
            'photo_path' => null,
            'links' => [
                ['label' => 'www', 'url' => fake()->url()],
            ],
        ];
    }
}
