import { computed } from 'vue';
import type { MaybeRefOrGetter } from 'vue';
import { toValue } from 'vue';
import { useI18n } from '@/composables/useI18n';

/**
 * Date formatting that always uses the event timezone, whatever the
 * timezone of the attendee's phone.
 */
export function useEventTime(timezone: MaybeRefOrGetter<string>) {
    const { locale } = useI18n();

    const formatters = computed(() => {
        const timeZone = toValue(timezone);
        const lang = locale.value;

        return {
            time: new Intl.DateTimeFormat(lang, {
                timeZone,
                hour: '2-digit',
                minute: '2-digit',
            }),
            dateTime: new Intl.DateTimeFormat(lang, {
                timeZone,
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit',
            }),
            // Calendar days are plain dates: format them in UTC so they never shift.
            weekday: new Intl.DateTimeFormat(lang, {
                timeZone: 'UTC',
                weekday: 'short',
            }),
            dayShort: new Intl.DateTimeFormat(lang, {
                timeZone: 'UTC',
                day: 'numeric',
                month: 'short',
            }),
            dayLong: new Intl.DateTimeFormat(lang, {
                timeZone: 'UTC',
                weekday: 'long',
                day: 'numeric',
                month: 'long',
            }),
        };
    });

    const dayToDate = (day: string) => new Date(`${day}T12:00:00Z`);

    return {
        formatTime: (iso: string) =>
            formatters.value.time.format(new Date(iso)),
        formatRange: (start: string, end: string) =>
            `${formatters.value.time.format(new Date(start))}–${formatters.value.time.format(new Date(end))}`,
        formatDateTime: (iso: string) =>
            formatters.value.dateTime.format(new Date(iso)),
        formatWeekday: (day: string) =>
            formatters.value.weekday.format(dayToDate(day)),
        formatDayShort: (day: string) =>
            formatters.value.dayShort.format(dayToDate(day)),
        formatDayLong: (day: string) =>
            formatters.value.dayLong.format(dayToDate(day)),
        /** Today's date (YYYY-MM-DD) in the event timezone. */
        todayInEvent: (now: Date = new Date()) =>
            new Intl.DateTimeFormat('en-CA', {
                timeZone: toValue(timezone),
            }).format(now),
    };
}
