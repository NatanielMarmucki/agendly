<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Picks the UI language for attendees: `?lang=` switches it and is
 * remembered in a purely functional cookie; otherwise the app default (pl).
 */
class SetLocale
{
    public const string COOKIE = 'agendly_locale';

    /**
     * @var list<string>
     */
    public const array SUPPORTED = ['pl', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $requested = $request->query('lang');

        if (is_string($requested) && in_array($requested, self::SUPPORTED, true)) {
            app()->setLocale($requested);
            Cookie::queue(self::COOKIE, $requested, 60 * 24 * 365, httpOnly: true);
        } elseif (in_array($cookie = $request->cookie(self::COOKIE), self::SUPPORTED, true)) {
            app()->setLocale((string) $cookie);
        }

        return $next($request);
    }
}
