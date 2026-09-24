<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import EmptyState from '@/components/public/EmptyState.vue';
import PageHeader from '@/components/public/PageHeader.vue';
import { useAnnouncements } from '@/composables/useAnnouncements';
import { useEventTime } from '@/composables/useEventTime';
import { useI18n } from '@/composables/useI18n';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { AnnouncementFeedData, PublicPageProps } from '@/types';

defineOptions({ layout: PublicLayout });

const props = defineProps<{ feed: AnnouncementFeedData }>();

const page = usePage<PublicPageProps>();
const event = computed(() => page.props.event);

const { t } = useI18n();
const { formatDateTime } = useEventTime(() => event.value.timezone);
const store = useAnnouncements(event.value.slug);

// Fresh server data seeds the shared store; polling keeps it current.
watch(
    () => props.feed,
    (value) => store.applyFeed(value),
    { immediate: true },
);

const announcements = computed(
    () => store.announcements.value ?? props.feed.announcements,
);

// Reading this page marks everything as seen (clears the nav dot).
watch(announcements, () => store.markAllSeen(), { immediate: true });
</script>

<template>
    <PageHeader :title="t('public.announcements.title')" />

    <ul v-if="announcements.length > 0" class="space-y-3" aria-live="polite">
        <li
            v-for="announcement in announcements"
            :key="announcement.id"
            class="rounded-2xl border p-4"
            :class="
                announcement.priority === 'important'
                    ? 'border-red-200 bg-red-50 dark:border-red-500/30 dark:bg-red-500/10'
                    : 'border-border bg-card'
            "
        >
            <div
                class="mb-1 flex items-center gap-2 text-xs text-muted-foreground"
            >
                <span
                    v-if="announcement.priority === 'important'"
                    class="rounded-full bg-red-600 px-2 py-0.5 font-semibold text-white"
                >
                    {{ t('public.announcements.important') }}
                </span>
                <time :datetime="announcement.publishedAt">
                    {{ formatDateTime(announcement.publishedAt) }}
                </time>
            </div>
            <h2 class="font-semibold">{{ announcement.title }}</h2>
            <p class="mt-1 text-sm leading-relaxed whitespace-pre-line">
                {{ announcement.body }}
            </p>
        </li>
    </ul>
    <EmptyState v-else :title="t('public.announcements.empty')" />
</template>
