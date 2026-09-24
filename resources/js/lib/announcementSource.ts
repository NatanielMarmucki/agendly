import type { AnnouncementFeedData } from '@/types';

/**
 * Where announcement updates come from.
 *
 * Today: HTTP polling of /e/{slug}/announcements.json. To move to Laravel
 * Reverb, add an implementation that listens on an Echo channel (e.g. fed
 * by the AnnouncementPublished event) and pass it to
 * startAnnouncementUpdates() – nothing else needs to change.
 */
export interface AnnouncementSource {
    /** Start delivering updates. Returns a function that stops them. */
    subscribe(onFeed: (feed: AnnouncementFeedData) => void): () => void;
}

export function createPollingSource(
    url: string,
    intervalMs = 60_000,
): AnnouncementSource {
    return {
        subscribe(onFeed) {
            let inFlight = false;

            const poll = async () => {
                if (
                    inFlight ||
                    document.visibilityState === 'hidden' ||
                    !navigator.onLine
                ) {
                    return;
                }

                inFlight = true;

                try {
                    const response = await fetch(url, {
                        headers: { Accept: 'application/json' },
                    });

                    if (response.ok) {
                        onFeed((await response.json()) as AnnouncementFeedData);
                    }
                } catch {
                    // Offline or flaky venue Wi-Fi: keep showing what we have.
                } finally {
                    inFlight = false;
                }
            };

            const onVisible = () => {
                if (document.visibilityState === 'visible') {
                    void poll();
                }
            };

            const timer = window.setInterval(() => void poll(), intervalMs);
            document.addEventListener('visibilitychange', onVisible);
            window.addEventListener('online', onVisible);

            return () => {
                window.clearInterval(timer);
                document.removeEventListener('visibilitychange', onVisible);
                window.removeEventListener('online', onVisible);
            };
        },
    };
}
