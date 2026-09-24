<script setup lang="ts">
import { MapPin, Users } from '@lucide/vue';
import EmptyState from '@/components/public/EmptyState.vue';
import PageHeader from '@/components/public/PageHeader.vue';
import { useI18n } from '@/composables/useI18n';
import PublicLayout from '@/layouts/PublicLayout.vue';
import type { GroupData } from '@/types';

defineOptions({ layout: PublicLayout });

defineProps<{ groups: GroupData[] }>();

const { t } = useI18n();
</script>

<template>
    <PageHeader
        :title="t('public.groups.title')"
        :subtitle="t('public.groups.hint')"
    />

    <ul v-if="groups.length > 0" class="grid gap-3 sm:grid-cols-2">
        <li
            v-for="group in groups"
            :key="group.id"
            class="rounded-2xl border border-border bg-card p-4"
        >
            <h2 class="font-semibold">{{ group.name }}</h2>
            <dl class="mt-2 space-y-1 text-sm">
                <div v-if="group.leaderName" class="flex items-center gap-2">
                    <dt class="sr-only">{{ t('public.groups.leader') }}</dt>
                    <Users class="size-4 text-muted-foreground" />
                    <dd>{{ group.leaderName }}</dd>
                </div>
                <div v-if="group.location" class="flex items-center gap-2">
                    <dt class="sr-only">{{ t('public.groups.location') }}</dt>
                    <MapPin class="size-4 text-muted-foreground" />
                    <dd>{{ group.location }}</dd>
                </div>
            </dl>
            <p
                v-if="group.description"
                class="mt-2 text-sm text-muted-foreground"
            >
                {{ group.description }}
            </p>
        </li>
    </ul>
    <EmptyState v-else :title="t('public.groups.empty')" />
</template>
