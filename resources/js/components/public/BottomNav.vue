<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { CalendarDays, Info, Megaphone, Star, Users } from '@lucide/vue';
import { computed } from 'vue';
import { useAnnouncements } from '@/composables/useAnnouncements';
import { useI18n } from '@/composables/useI18n';
import publicRoutes from '@/routes/public';

const props = defineProps<{ eventSlug: string }>();

const { t } = useI18n();
const page = usePage();
const { hasUnread } = useAnnouncements(props.eventSlug);

const path = computed(() => page.url.split(/[?#]/)[0]);

const items = computed(() => {
    const slug = props.eventSlug;
    const base = publicRoutes.schedule(slug).url;

    return [
        {
            href: base,
            label: t('public.nav.schedule'),
            icon: CalendarDays,
            active:
                path.value === base ||
                path.value.startsWith(`${base}/sessions`),
            dot: false,
        },
        {
            href: publicRoutes.plan(slug).url,
            label: t('public.nav.plan'),
            icon: Star,
            active: path.value === publicRoutes.plan(slug).url,
            dot: false,
        },
        {
            href: publicRoutes.announcements(slug).url,
            label: t('public.nav.announcements'),
            icon: Megaphone,
            active: path.value === publicRoutes.announcements(slug).url,
            dot: hasUnread.value,
        },
        {
            href: publicRoutes.speakers(slug).url,
            label: t('public.nav.speakers'),
            icon: Users,
            active: path.value === publicRoutes.speakers(slug).url,
            dot: false,
        },
        {
            href: publicRoutes.info(slug).url,
            label: t('public.nav.more'),
            icon: Info,
            active:
                path.value === publicRoutes.info(slug).url ||
                path.value === publicRoutes.groups(slug).url,
            dot: false,
        },
    ];
});
</script>

<template>
    <nav
        class="fixed inset-x-0 bottom-0 z-20 border-t border-border bg-background/95 pb-[env(safe-area-inset-bottom)] backdrop-blur"
    >
        <ul class="mx-auto grid max-w-2xl grid-cols-5">
            <li v-for="item in items" :key="item.href">
                <Link
                    :href="item.href"
                    class="relative flex h-16 flex-col items-center justify-center gap-1 text-[11px] font-medium"
                    :class="
                        item.active ? 'text-primary' : 'text-muted-foreground'
                    "
                    :aria-current="item.active ? 'page' : undefined"
                >
                    <component :is="item.icon" class="size-5" />
                    {{ item.label }}
                    <span
                        v-if="item.dot"
                        class="absolute top-3 left-1/2 ml-2 size-2 rounded-full bg-red-500"
                    />
                </Link>
            </li>
        </ul>
    </nav>
</template>
