<script setup lang="ts">
import { useRemember, usePage } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import ChipGroup from '@/components/public/ChipGroup.vue';
import EmptyState from '@/components/public/EmptyState.vue';
import PageHeader from '@/components/public/PageHeader.vue';
import SessionCard from '@/components/public/SessionCard.vue';
import { useEventTime } from '@/composables/useEventTime';
import { useI18n } from '@/composables/useI18n';
import { useNow } from '@/composables/useNow';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type {
    PublicPageProps,
    ScheduleData,
    SessionData,
    SessionType,
} from '@/types';

defineOptions({ layout: PublicLayout });

const props = defineProps<{ schedule: ScheduleData }>();

const page = usePage<PublicPageProps>();
const event = computed(() => page.props.event);

const { t } = useI18n();
const now = useNow();
const { formatWeekday, formatDayShort, todayInEvent } = useEventTime(
    () => event.value.timezone,
);

const time = (iso: string) => new Date(iso).getTime();

// --- Now / Next -----------------------------------------------------------

const happeningNow = computed(() =>
    props.schedule.sessions.filter(
        (s) =>
            time(s.startsAt) <= now.value.getTime() &&
            now.value.getTime() < time(s.endsAt),
    ),
);

const upNext = computed<SessionData[]>(() => {
    const upcoming = props.schedule.sessions.filter(
        (s) => time(s.startsAt) > now.value.getTime(),
    );

    if (upcoming.length === 0) {
        return [];
    }

    const first = Math.min(...upcoming.map((s) => time(s.startsAt)));

    return upcoming.filter((s) => time(s.startsAt) === first);
});

const minutesUntil = (iso: string) =>
    Math.max(1, Math.round((time(iso) - now.value.getTime()) / 60_000));

const eventOver = computed(
    () =>
        props.schedule.sessions.length > 0 &&
        now.value.getTime() >= time(event.value.endsAt) &&
        happeningNow.value.length === 0 &&
        upNext.value.length === 0,
);

// --- Day tabs & filters (remembered when coming back from a session) ------

const defaultDay = () => {
    const today = todayInEvent();

    return event.value.days.includes(today) ? today : event.value.days[0];
};

type ScheduleFilters = {
    day: string;
    room: number | null;
    type: SessionType | null;
};

const state = useRemember(
    reactive<ScheduleFilters>({ day: defaultDay(), room: null, type: null }),
    `schedule:${event.value.slug}`,
) as ScheduleFilters;

const roomOptions = computed(() => [
    { value: null, label: t('public.common.all') },
    ...props.schedule.rooms.map((room) => ({
        value: room.id,
        label: room.name,
    })),
]);

const typeOptions = computed(() => {
    const present = new Set(props.schedule.sessions.map((s) => s.type));

    return [
        { value: null, label: t('public.common.all') },
        ...[...present].map((type) => ({
            value: type,
            label: t(`enums.session_type.${type}`),
        })),
    ];
});

const visibleSessions = computed(() =>
    props.schedule.sessions.filter(
        (s) =>
            s.day === state.day &&
            (state.room === null || s.room?.id === state.room) &&
            (state.type === null || s.type === state.type),
    ),
);

const isNow = (session: SessionData) =>
    happeningNow.value.some((s) => s.id === session.id);
</script>

<template>
    <PageHeader :title="t('public.schedule.title')" :subtitle="event.venue" />

    <section
        v-if="happeningNow.length > 0 || upNext.length > 0"
        class="mb-6 space-y-4"
        aria-live="polite"
    >
        <div v-if="happeningNow.length > 0">
            <h2
                class="mb-2 flex items-center gap-2 text-sm font-semibold tracking-wide text-primary uppercase"
            >
                <span class="relative flex size-2">
                    <span
                        class="absolute inline-flex size-full animate-ping rounded-full bg-primary opacity-60"
                    />
                    <span
                        class="relative inline-flex size-2 rounded-full bg-primary"
                    />
                </span>
                {{ t('public.schedule.now') }}
            </h2>
            <div class="space-y-2">
                <SessionCard
                    v-for="session in happeningNow"
                    :key="session.id"
                    :event="event"
                    :session="session"
                    highlighted
                />
            </div>
        </div>

        <div v-if="upNext.length > 0">
            <h2
                class="mb-2 text-sm font-semibold tracking-wide text-muted-foreground uppercase"
            >
                {{ t('public.schedule.next') }}
                <span class="font-normal normal-case">
                    ·
                    {{
                        t('public.schedule.starts_in', {
                            minutes: minutesUntil(upNext[0].startsAt),
                        })
                    }}
                </span>
            </h2>
            <div class="space-y-2">
                <SessionCard
                    v-for="session in upNext"
                    :key="session.id"
                    :event="event"
                    :session="session"
                />
            </div>
        </div>
    </section>

    <p
        v-else-if="eventOver"
        class="mb-6 rounded-2xl bg-secondary p-4 text-center text-sm"
    >
        {{ t('public.schedule.event_over') }}
    </p>

    <div
        role="tablist"
        class="-mx-4 mb-3 flex gap-2 overflow-x-auto px-4"
        :aria-label="t('public.schedule.title')"
    >
        <button
            v-for="day in event.days"
            :key="day"
            role="tab"
            type="button"
            class="flex min-w-16 flex-col items-center rounded-xl border px-3 py-1.5 text-sm transition focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
            :class="
                state.day === day
                    ? 'border-primary bg-primary text-primary-foreground'
                    : 'border-border bg-card'
            "
            :aria-selected="state.day === day"
            @click="state.day = day"
        >
            <span class="text-xs capitalize opacity-80">{{
                formatWeekday(day)
            }}</span>
            <span class="font-semibold">{{ formatDayShort(day) }}</span>
        </button>
    </div>

    <div class="mb-4 space-y-2">
        <ChipGroup
            v-if="schedule.rooms.length > 1"
            v-model="state.room"
            :label="t('public.schedule.filter_room')"
            :options="roomOptions"
        />
        <ChipGroup
            v-model="state.type"
            :label="t('public.schedule.filter_type')"
            :options="typeOptions"
        />
    </div>

    <div v-if="visibleSessions.length > 0" class="space-y-2">
        <SessionCard
            v-for="session in visibleSessions"
            :key="session.id"
            :event="event"
            :session="session"
            :highlighted="isNow(session)"
        />
    </div>
    <EmptyState v-else :title="t('public.schedule.empty')" />
</template>
