<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronLeft, Clock, ExternalLink, MapPin } from '@lucide/vue';
import { computed } from 'vue';
import PageHeader from '@/components/public/PageHeader.vue';
import PlanToggle from '@/components/public/PlanToggle.vue';
import SpeakerAvatar from '@/components/public/SpeakerAvatar.vue';
import TypeBadge from '@/components/public/TypeBadge.vue';
import { useEventTime } from '@/composables/useEventTime';
import { useI18n } from '@/composables/useI18n';
import PublicLayout from '@/layouts/PublicLayout.vue';
import publicRoutes from '@/routes/public';
import type { PublicPageProps, SessionData, SpeakerData } from '@/types';

defineOptions({ layout: PublicLayout });

const props = defineProps<{
    session: SessionData;
    speakers: SpeakerData[];
}>();

const page = usePage<PublicPageProps>();
const event = computed(() => page.props.event);

const { t } = useI18n();
const { formatDayLong, formatRange } = useEventTime(() => event.value.timezone);

const when = computed(
    () =>
        `${formatDayLong(props.session.day)}, ${formatRange(props.session.startsAt, props.session.endsAt)}`,
);
</script>

<template>
    <PageHeader :title="session.title">
        <template #before>
            <Link
                :href="publicRoutes.schedule(event.slug)"
                class="mb-3 -ml-1 inline-flex items-center gap-1 text-sm text-muted-foreground"
            >
                <ChevronLeft class="size-4" />
                {{ t('public.nav.schedule') }}
            </Link>
            <div class="mb-2">
                <TypeBadge :type="session.type" />
            </div>
        </template>
    </PageHeader>

    <dl class="mb-6 space-y-3 rounded-2xl border border-border bg-card p-4">
        <div class="flex gap-3">
            <dt class="sr-only">{{ t('public.info.when') }}</dt>
            <Clock class="mt-0.5 size-5 shrink-0 text-muted-foreground" />
            <dd class="first-letter:uppercase">{{ when }}</dd>
        </div>
        <div class="flex gap-3">
            <dt class="sr-only">{{ t('public.session.room') }}</dt>
            <MapPin class="mt-0.5 size-5 shrink-0 text-muted-foreground" />
            <dd>
                <template v-if="session.room">
                    <p>{{ session.room.name }}</p>
                    <p
                        v-if="session.room.description"
                        class="text-sm text-muted-foreground"
                    >
                        {{ session.room.description }}
                    </p>
                </template>
                <template v-else>{{ t('public.session.no_room') }}</template>
            </dd>
        </div>
    </dl>

    <div class="mb-6 flex flex-col gap-2 sm:flex-row">
        <PlanToggle
            v-if="session.plannable"
            :event-slug="event.slug"
            :session-id="session.id"
            variant="button"
        />
    </div>

    <p
        v-if="session.description"
        class="mb-8 leading-relaxed whitespace-pre-line"
    >
        {{ session.description }}
    </p>

    <section v-if="speakers.length > 0">
        <h2 class="mb-3 text-lg font-semibold">
            {{ t('public.session.speakers') }}
        </h2>
        <ul class="space-y-3">
            <li
                v-for="speaker in speakers"
                :key="speaker.id"
                class="flex gap-3 rounded-2xl border border-border bg-card p-4"
            >
                <SpeakerAvatar
                    :name="speaker.name"
                    :photo-url="speaker.photoUrl"
                />
                <div class="min-w-0">
                    <Link
                        :href="`${publicRoutes.speakers(event.slug).url}#speaker-${speaker.id}`"
                        class="font-medium"
                    >
                        {{ speaker.name }}
                    </Link>
                    <p
                        v-if="speaker.bio"
                        class="mt-1 line-clamp-3 text-sm text-muted-foreground"
                    >
                        {{ speaker.bio }}
                    </p>
                    <div
                        v-if="speaker.links.length > 0"
                        class="mt-2 flex flex-wrap gap-3 text-sm"
                    >
                        <a
                            v-for="link in speaker.links"
                            :key="link.url"
                            :href="link.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1 text-primary"
                        >
                            {{ link.label }}
                            <ExternalLink class="size-3" />
                        </a>
                    </div>
                </div>
            </li>
        </ul>
    </section>
</template>
