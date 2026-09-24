<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Clock, MapPin, ShieldCheck, Users } from '@lucide/vue';
import { computed } from 'vue';
import PageHeader from '@/components/public/PageHeader.vue';
import { useEventTime } from '@/composables/useEventTime';
import { useI18n } from '@/composables/useI18n';
import PublicLayout from '@/layouts/PublicLayout.vue';
import publicRoutes from '@/routes/public';
import type { PublicPageProps, RoomData } from '@/types';

defineOptions({ layout: PublicLayout });

defineProps<{ rooms: RoomData[] }>();

const page = usePage<PublicPageProps>();
const event = computed(() => page.props.event);

const { t } = useI18n();
const { formatDateTime } = useEventTime(() => event.value.timezone);

const mapUrl = computed(() =>
    event.value.venue
        ? `https://www.openstreetmap.org/search?query=${encodeURIComponent(event.value.venue)}`
        : null,
);
</script>

<template>
    <img
        v-if="event.coverImageUrl"
        :src="event.coverImageUrl"
        alt=""
        class="mb-4 aspect-[2/1] w-full rounded-2xl object-cover"
    />

    <PageHeader :title="event.name" />

    <p
        v-if="event.description"
        class="mb-6 leading-relaxed whitespace-pre-line"
    >
        {{ event.description }}
    </p>

    <dl class="mb-6 space-y-3 rounded-2xl border border-border bg-card p-4">
        <div class="flex gap-3">
            <Clock class="mt-0.5 size-5 shrink-0 text-muted-foreground" />
            <div>
                <dt class="text-xs text-muted-foreground">
                    {{ t('public.info.when') }}
                </dt>
                <dd>
                    {{ formatDateTime(event.startsAt) }} –
                    {{ formatDateTime(event.endsAt) }}
                </dd>
            </div>
        </div>
        <div v-if="event.venue" class="flex gap-3">
            <MapPin class="mt-0.5 size-5 shrink-0 text-muted-foreground" />
            <div>
                <dt class="text-xs text-muted-foreground">
                    {{ t('public.info.where') }}
                </dt>
                <dd>
                    {{ event.venue }}
                    <a
                        v-if="mapUrl"
                        :href="mapUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="block text-sm text-primary"
                    >
                        {{ t('public.info.open_map') }}
                    </a>
                </dd>
            </div>
        </div>
    </dl>

    <div class="mb-6 grid gap-2">
        <Link
            :href="publicRoutes.groups(event.slug)"
            class="flex items-center gap-3 rounded-2xl border border-border bg-card p-4 font-medium"
        >
            <Users class="size-5 text-muted-foreground" />
            {{ t('public.nav.groups') }}
        </Link>
    </div>

    <section v-if="rooms.length > 0" class="mb-6">
        <h2 class="mb-2 text-lg font-semibold">{{ t('public.info.rooms') }}</h2>
        <ul
            class="divide-y divide-border rounded-2xl border border-border bg-card"
        >
            <li v-for="room in rooms" :key="room.id" class="p-4">
                <p class="font-medium">{{ room.name }}</p>
                <p
                    v-if="room.description"
                    class="text-sm text-muted-foreground"
                >
                    {{ room.description }}
                </p>
            </li>
        </ul>
    </section>

    <section class="rounded-2xl bg-secondary p-4 text-sm">
        <h2 class="mb-1 flex items-center gap-2 font-semibold">
            <ShieldCheck class="size-4" />
            {{ t('public.info.privacy_title') }}
        </h2>
        <p class="text-muted-foreground">{{ t('public.info.privacy') }}</p>
    </section>
</template>
