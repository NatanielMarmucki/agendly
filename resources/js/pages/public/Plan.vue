<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ShieldCheck } from '@lucide/vue';
import { computed } from 'vue';
import EmptyState from '@/components/public/EmptyState.vue';
import PageHeader from '@/components/public/PageHeader.vue';
import SessionCard from '@/components/public/SessionCard.vue';
import { useEventTime } from '@/composables/useEventTime';
import { useI18n } from '@/composables/useI18n';
import { usePlan } from '@/composables/usePlan';
import PublicLayout from '@/layouts/PublicLayout.vue';
import publicRoutes from '@/routes/public';
import type { PublicPageProps, ScheduleData, SessionData } from '@/types';

defineOptions({ layout: PublicLayout });

const props = defineProps<{ schedule: ScheduleData }>();

const page = usePage<PublicPageProps>();
const event = computed(() => page.props.event);

const { t } = useI18n();
const { formatDayLong } = useEventTime(() => event.value.timezone);
const plan = usePlan(event.value.slug);

const planned = computed(() =>
    props.schedule.sessions.filter((session) => plan.has(session.id)),
);

const days = computed(() => {
    const byDay = new Map<string, SessionData[]>();

    for (const session of planned.value) {
        byDay.set(session.day, [...(byDay.get(session.day) ?? []), session]);
    }

    return [...byDay.entries()].map(([day, sessions]) => ({ day, sessions }));
});

const conflicts = computed(() => {
    const ids = new Set<number>();
    const list = planned.value;

    for (let i = 0; i < list.length; i++) {
        for (let j = i + 1; j < list.length; j++) {
            const a = list[i];
            const b = list[j];

            if (a.startsAt < b.endsAt && b.startsAt < a.endsAt) {
                ids.add(a.id);
                ids.add(b.id);
            }
        }
    }

    return ids;
});

function clearPlan(): void {
    if (window.confirm(t('public.plan.clear_confirm'))) {
        plan.clear();
    }
}
</script>

<template>
    <PageHeader :title="t('public.plan.title')" />

    <EmptyState
        v-if="planned.length === 0"
        :title="t('public.plan.empty_title')"
        :body="t('public.plan.empty_body')"
    >
        <Link
            :href="publicRoutes.schedule(event.slug)"
            class="inline-flex h-10 items-center rounded-xl bg-primary px-4 text-sm font-medium text-primary-foreground"
        >
            {{ t('public.plan.browse') }}
        </Link>
    </EmptyState>

    <template v-else>
        <section v-for="group in days" :key="group.day" class="mb-6">
            <h2
                class="mb-2 text-sm font-semibold text-muted-foreground first-letter:uppercase"
            >
                {{ formatDayLong(group.day) }}
            </h2>
            <div class="space-y-2">
                <SessionCard
                    v-for="session in group.sessions"
                    :key="session.id"
                    :event="event"
                    :session="session"
                    :conflict="conflicts.has(session.id)"
                />
            </div>
        </section>

        <div class="flex flex-wrap gap-2">
            <button
                type="button"
                class="h-10 rounded-xl border border-border px-4 text-sm"
                @click="clearPlan"
            >
                {{ t('public.plan.clear') }}
            </button>
        </div>
    </template>

    <p class="mt-8 flex items-start gap-2 text-xs text-muted-foreground">
        <ShieldCheck class="size-4 shrink-0" />
        {{ t('public.plan.privacy') }}
    </p>
</template>
