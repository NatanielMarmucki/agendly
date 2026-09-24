export type Locale = 'pl' | 'en';

/**
 * Props shared with every Inertia page (see HandleInertiaRequests).
 */
export type SharedProps = {
    name: string;
    locale: Locale;
    availableLocales: Locale[];
    translations: Record<string, string>;
    [key: string]: unknown;
};
