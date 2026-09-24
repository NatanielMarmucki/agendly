<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ExternalLink } from '@lucide/vue';
import { computed } from 'vue';
import EmptyState from '@/components/public/EmptyState.vue';
import PageHeader from '@/components/public/PageHeader.vue';
import SpeakerAvatar from '@/components/public/SpeakerAvatar.vue';
import { useI18n } from '@/composables/useI18n';
import PublicLayout from '@/layouts/PublicLayout.vue';
import publicRoutes from '@/routes/public';
import type { PublicPageProps, SpeakerData } from '@/types';

defineOptions({ layout: PublicLayout });

defineProps<{ speakers: SpeakerData[] }>();

const page = usePage<PublicPageProps>();
const event = computed(() => page.props.event);
const { t } = useI18n();
</script>

<template>
    <PageHeader :title="t('public.speakers.title')" />

    <ul v-if="speakers.length > 0" class="space-y-3">
        <li
            v-for="speaker in speakers"
            :id="`speaker-${speaker.id}`"
            :key="speaker.id"
            class="scroll-mt-20 rounded-2xl border border-border bg-card p-4"
        >
            <div class="flex items-center gap-3">
                <SpeakerAvatar
                    :name="speaker.name"
                    :photo-url="speaker.photoUrl"
                    size="lg"
                />
                <h2 class="text-lg font-semibold">{{ speaker.name }}</h2>
            </div>
            <p
                v-if="speaker.bio"
                class="mt-3 text-sm leading-relaxed whitespace-pre-line"
            >
                {{ speaker.bio }}
            </p>
            <div v-if="speaker.sessions.length > 0" class="mt-3">
                <h3
                    class="text-xs font-semibold text-muted-foreground uppercase"
                >
                    {{ t('public.speakers.sessions') }}
                </h3>
                <ul class="mt-1 space-y-1 text-sm">
                    <li v-for="session in speaker.sessions" :key="session.id">
                        <Link
                            :href="
                                publicRoutes.sessions.show({
                                    event: event.slug,
                                    session: session.id,
                                })
                            "
                            class="text-primary"
                        >
                            {{ session.title }}
                        </Link>
                    </li>
                </ul>
            </div>
            <div
                v-if="speaker.links.length > 0"
                class="mt-3 flex flex-wrap gap-3 text-sm"
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
        </li>
    </ul>
    <EmptyState v-else :title="t('public.speakers.empty')" />
</template>
