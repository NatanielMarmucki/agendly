<?php

declare(strict_types=1);

namespace App\Data\Public;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Base for the typed Inertia props of the public app. Every DTO mirrors a
 * TypeScript type in resources/js/types/public.ts.
 *
 * @implements Arrayable<string, mixed>
 */
abstract readonly class Data implements Arrayable, JsonSerializable
{
    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @template TData of Data
     *
     * @param  iterable<TData>  $items
     * @return list<array<string, mixed>>
     */
    protected static function many(iterable $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = $item->toArray();
        }

        return $result;
    }
}
