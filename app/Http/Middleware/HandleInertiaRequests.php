<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'locale' => app()->getLocale(),
            'availableLocales' => SetLocale::SUPPORTED,
            'translations' => fn (): array => $this->translations(),
        ];
    }

    /**
     * UI strings for the Vue app, flattened to dot keys ("public.nav.plan").
     * Always sent in full: they are small and must be present in every
     * response the service worker caches for offline use.
     *
     * @return array<string, string>
     */
    private function translations(): array
    {
        /** @var array<string, string> $flat */
        $flat = Arr::dot([
            'public' => trans('public'),
            'enums' => trans('enums'),
        ]);

        return $flat;
    }
}
