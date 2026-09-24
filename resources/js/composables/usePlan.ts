import { useLocalStorage } from '@vueuse/core';
import { computed } from 'vue';

/**
 * "My plan" – a list of session ids kept in localStorage only.
 * Nothing about it ever leaves the device.
 */
export function usePlan(eventSlug: string) {
    const ids = useLocalStorage<number[]>(`agendly:plan:${eventSlug}`, []);

    const set = computed(() => new Set(ids.value));

    function has(id: number): boolean {
        return set.value.has(id);
    }

    function toggle(id: number): void {
        ids.value = has(id)
            ? ids.value.filter((existing) => existing !== id)
            : [...ids.value, id];
    }

    function clear(): void {
        ids.value = [];
    }

    return { ids, has, toggle, clear, count: computed(() => set.value.size) };
}
