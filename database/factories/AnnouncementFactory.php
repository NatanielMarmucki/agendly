<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AnnouncementPriority;
use App\Models\Announcement;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'priority' => AnnouncementPriority::Normal,
            'published_at' => now()->subMinutes(fake()->numberBetween(1, 600)),
        ];
    }

    public function important(): static
    {
        return $this->state(fn (): array => ['priority' => AnnouncementPriority::Important]);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['published_at' => null]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (): array => ['published_at' => now()->addDay()]);
    }
}
