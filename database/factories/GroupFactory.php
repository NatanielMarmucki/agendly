<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => 'Grupa '.fake()->unique()->numberBetween(1, 99),
            'leader_name' => fake()->name(),
            'location' => fake()->optional()->streetName(),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
