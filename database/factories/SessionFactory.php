<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SessionType;
use App\Models\Event;
use App\Models\Session;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Session>
 */
class SessionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = CarbonImmutable::instance(fake()->dateTimeBetween('+1 week', '+2 weeks'))
            ->setTime(fake()->numberBetween(8, 20), 0);

        return [
            'event_id' => Event::factory(),
            'room_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addMinutes(60),
            'type' => fake()->randomElement(SessionType::cases()),
            'sort_order' => 0,
        ];
    }

    /**
     * Place the session inside the given event, starting at a local wall-clock time.
     */
    public function at(Event $event, string $localDateTime, int $minutes = 60): static
    {
        $startsAt = CarbonImmutable::parse($localDateTime, $event->timezone);

        return $this->state(fn (): array => [
            'event_id' => $event->id,
            'starts_at' => $startsAt->utc(),
            'ends_at' => $startsAt->addMinutes($minutes)->utc(),
        ]);
    }
}
