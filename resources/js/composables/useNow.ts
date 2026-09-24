import { useNow as useVueUseNow } from '@vueuse/core';

/**
 * The current time, refreshed every 30 s – enough for "Now / Next".
 */
export function useNow() {
    return useVueUseNow({ interval: 30_000 });
}
