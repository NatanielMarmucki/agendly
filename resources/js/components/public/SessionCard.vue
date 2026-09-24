<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { MapPin, TriangleAlert } from '@lucide/vue';
import { computed } from 'vue';
import PlanToggle from '@/components/public/PlanToggle.vue';
import TypeBadge from '@/components/public/TypeBadge.vue';
import { useEventTime } from '@/composables/useEventTime';
import { useI18n } from '@/composables/useI18n';
import publicRoutes from '@/routes/public';
import type { EventData, SessionData } from '@/types';

const props = defineProps<{
    event: EventData;
    session: SessionData;
    highlighted?: boolean;
    conflict?: boolean;
}>();

const { t } = useI18n();
const { formatTime } = useEventTime(() => props.event.timezone);

const speakers = computed(() =>
    props.session.speakers.map((speaker) => speaker.name).join(', '),
);
const muted = computed(() => !props.session.plannable);
</script>

<template>
    <article
        class="flex items-start gap-3 rounded-2xl border p-3"
        :class="[
            highlighted
                ? 'border-primary/40 bg-primary/5'
                : 'border-border bg-card',
            muted ? 'opacity-80' : '',
        ]"
    >
        <div class="w-12 shrink-0 pt-0.5 text-sm tabular-nums">
            <div class="font-semibold">{{ formatTime(session.startsAt) }}</div>
            <div class="text-muted-foreground">
                {{ formatTime(session.endsAt) }}
            </div>
        </div>

        <Link
            :href="
                publicRoutes.sessions.show({
                    event: event.slug,
                    session: session.id,
                })
            "
            class="min-w-0 flex-1 rounded-lg focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
        >
            <TypeBadge :type="session.type" />
            <h3 class="mt-1 leading-snug font-medium">{{ session.title }}</h3>
            <p
                v-if="session.room || speakers"
                class="mt-1 flex flex-wrap items-center gap-x-2 text-sm text-muted-foreground"
            >
                <span
                    v-if="session.room"
                    class="inline-flex items-center gap-1"
                >
                    <MapPin class="size-3.5" />{{ session.room.name }}
                </span>
                <span v-if="speakers">{{ speakers }}</span>
            </p>
            <p
                v-if="conflict"
                class="mt-1 inline-flex items-center gap-1 text-sm text-amber-700 dark:text-amber-300"
            >
                <TriangleAlert class="size-3.5" />
                {{ t('public.plan.conflict') }}
            </p>
        </Link>

        <PlanToggle
            v-if="session.plannable"
            :event-slug="event.slug"
            :session-id="session.id"
        />
    </article>
</template>
