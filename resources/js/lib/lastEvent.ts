const KEY = 'agendly:last-event';

/**
 * Remembers the last opened event so the installed app (start_url "/")
 * can reopen it. Stored on the device only.
 */
export function rememberLastEvent(slug: string): void {
    try {
        localStorage.setItem(KEY, slug);
    } catch {
        // Private mode / storage disabled – not essential.
    }
}

export function lastEvent(): string | null {
    try {
        return localStorage.getItem(KEY);
    } catch {
        return null;
    }
}
