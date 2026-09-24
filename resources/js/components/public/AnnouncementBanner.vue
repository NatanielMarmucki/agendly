<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Megaphone, X } from '@lucide/vue';
import { useAnnouncements } from '@/composables/useAnnouncements';
import { useI18n } from '@/composables/useI18n';
import publicRoutes from '@/routes/public';

const props = defineProps<{ eventSlug: string }>();

const { t } = useI18n();
const { banner, dismissBanner } = useAnnouncements(props.eventSlug);
</script>

<template>
    <div
        v-if="banner"
        role="status"
        class="mx-auto mt-3 flex max-w-2xl items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-3 text-red-900 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-100"
    >
        <Megaphone class="mt-0.5 size-5 shrink-0" />
        <Link
            :href="publicRoutes.announcements(eventSlug)"
            class="min-w-0 flex-1"
        >
            <p class="font-semibold">{{ banner.title }}</p>
            <p class="line-clamp-2 text-sm">{{ banner.body }}</p>
        </Link>
        <button
            type="button"
            class="-m-1 rounded-full p-1 hover:bg-red-100 dark:hover:bg-red-500/20"
            :aria-label="t('public.common.dismiss')"
            @click="dismissBanner"
        >
            <X class="size-4" />
        </button>
    </div>
</template>
