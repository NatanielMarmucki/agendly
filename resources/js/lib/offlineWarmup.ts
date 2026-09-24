import publicRoutes from '@/routes/public';
import type { ScheduleData } from '@/types';

/**
 * After the first visit, quietly fetch every page of the event (both as
 * HTML and as Inertia JSON) so the service worker caches them and the whole
 * app works offline – including pages the attendee has not opened yet.
 *
 * Runs once per event and asset version, sequentially, to be gentle on
 * crowded venue Wi-Fi.
 */
export async function warmUpEventCache(
    slug: string,
    inertiaVersion: string | null,
): Promise<void> {
    if (!('serviceWorker' in navigator) || !navigator.onLine) {
        return;
    }

    const marker = `agendly:warm:${slug}:${inertiaVersion ?? ''}`;

    try {
        if (sessionStorage.getItem(marker)) {
            return;
        }
    } catch {
        return;
    }

    await navigator.serviceWorker.ready;

    const inertiaHeaders: Record<string, string> = {
        'X-Inertia': 'true',
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'text/html, application/xhtml+xml',
    };

    if (inertiaVersion) {
        inertiaHeaders['X-Inertia-Version'] = inertiaVersion;
    }

    const fetchBoth = async (url: string): Promise<Response | null> => {
        try {
            await fetch(url, { credentials: 'same-origin' });

            return await fetch(url, {
                credentials: 'same-origin',
                headers: inertiaHeaders,
            });
        } catch {
            return null;
        }
    };

    const schedule = await fetchBoth(publicRoutes.schedule(slug).url);
    let sessionIds: number[] = [];

    if (schedule?.ok) {
        try {
            const page = (await schedule.json()) as {
                props: { schedule: ScheduleData };
            };
            sessionIds = page.props.schedule.sessions.map((s) => s.id);
        } catch {
            // Not an Inertia payload (e.g. version mismatch) – skip sessions.
        }
    }

    const urls = [
        publicRoutes.plan(slug).url,
        publicRoutes.announcements(slug).url,
        publicRoutes.speakers(slug).url,
        publicRoutes.groups(slug).url,
        publicRoutes.info(slug).url,
        ...sessionIds.map(
            (session) =>
                publicRoutes.sessions.show({ event: slug, session }).url,
        ),
    ];

    for (const url of urls) {
        await fetchBoth(url);
    }

    try {
        await fetch(publicRoutes.announcements.feed(slug).url, {
            headers: { Accept: 'application/json' },
        });
        sessionStorage.setItem(marker, '1');
    } catch {
        // Try again on the next visit.
    }
}
