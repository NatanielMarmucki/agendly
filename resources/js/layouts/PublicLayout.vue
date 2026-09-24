<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, watch } from 'vue';
import AnnouncementBanner from '@/components/public/AnnouncementBanner.vue';
import BottomNav from '@/components/public/BottomNav.vue';
import InstallHint from '@/components/public/InstallHint.vue';
import LanguageSwitch from '@/components/public/LanguageSwitch.vue';
import OfflineNotice from '@/components/public/OfflineNotice.vue';
import {
    startAnnouncementUpdates,
    useAnnouncements,
} from '@/composables/useAnnouncements';
import { createPollingSource } from '@/lib/announcementSource';
import { rememberLastEvent } from '@/lib/lastEvent';
import { warmUpEventCache } from '@/lib/offlineWarmup';
import publicRoutes from '@/routes/public';
import type { PublicPageProps } from '@/types';

const page = usePage<PublicPageProps>();
const event = computed(() => page.props.event);

const { applyBanner } = useAnnouncements(event.value.slug);

// Every server response carries the current banner; polling refreshes it in between.
watch(
    () => page.props.banner,
    (banner) => applyBanner(banner),
    { immediate: true },
);

let stopUpdates: (() => void) | null = null;

onMounted(() => {
    const slug = event.value.slug;

    rememberLastEvent(slug);
    stopUpdates = startAnnouncementUpdates(
        slug,
        createPollingSource(publicRoutes.announcements.feed(slug).url),
    );

    // Make the whole event available offline after this first visit.
    window.setTimeout(() => void warmUpEventCache(slug, page.version), 3_000);
});

onUnmounted(() => stopUpdates?.());
</script>

<template>
    <div class="min-h-dvh bg-background text-foreground">
        <OfflineNotice />
        <header
            class="sticky top-0 z-10 border-b border-border bg-background/95 pt-[env(safe-area-inset-top)] backdrop-blur"
        >
            <div
                class="mx-auto flex h-12 max-w-2xl items-center justify-between gap-3 px-4"
            >
                <Link
                    :href="publicRoutes.schedule(event.slug)"
                    class="truncate font-semibold"
                >
                    {{ event.name }}
                </Link>
                <LanguageSwitch />
            </div>
        </header>

        <div class="px-4">
            <AnnouncementBanner :event-slug="event.slug" />
        </div>

        <main class="mx-auto max-w-2xl px-4 pt-4 pb-28">
            <slot />
        </main>

        <InstallHint />
        <BottomNav :event-slug="event.slug" />
    </div>
</template>
