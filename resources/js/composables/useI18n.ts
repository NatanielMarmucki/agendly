import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

/**
 * Minimal i18n on top of Laravel's lang files, which are shared as
 * flattened dot keys (see HandleInertiaRequests::translations()).
 */
export function useI18n() {
    const page = usePage();

    const locale = computed(() => page.props.locale);

    function t(
        key: string,
        replacements: Record<string, string | number> = {},
    ): string {
        let value = page.props.translations[key] ?? key;

        for (const [name, replacement] of Object.entries(replacements)) {
            value = value.replaceAll(`:${name}`, String(replacement));
        }

        return value;
    }

    return { t, locale };
}
