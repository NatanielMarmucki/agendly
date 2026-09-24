<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $startsAt = CarbonImmutable::instance(fake()->dateTimeBetween('+1 week', '+3 months'))
            ->setTimezone(Event::DEFAULT_TIMEZONE)
            ->setTime(9, 0);

        return [
            'user_id' => User::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'description' => fake()->paragraph(),
            'venue' => fake()->city(),
            'starts_at' => $startsAt->utc(),
            'ends_at' => $startsAt->addDays(2)->setTime(18, 0)->utc(),
            'timezone' => Event::DEFAULT_TIMEZONE,
            'is_published' => true,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn (): array => ['is_published' => false]);
    }
}
