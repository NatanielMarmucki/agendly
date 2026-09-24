<script setup lang="ts">
import { Star } from '@lucide/vue';
import { computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import { usePlan } from '@/composables/usePlan';

const props = withDefaults(
    defineProps<{
        eventSlug: string;
        sessionId: number;
        variant?: 'icon' | 'button';
    }>(),
    { variant: 'icon' },
);

const { t } = useI18n();
const plan = usePlan(props.eventSlug);
const inPlan = computed(() => plan.has(props.sessionId));
const label = computed(() =>
    inPlan.value
        ? t('public.session.remove_from_plan')
        : t('public.session.add_to_plan'),
);
</script>

<template>
    <button
        v-if="variant === 'icon'"
        type="button"
        class="-m-2 flex size-11 shrink-0 items-center justify-center rounded-full text-muted-foreground transition hover:bg-accent focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
        :aria-pressed="inPlan"
        :aria-label="label"
        :title="label"
        @click="plan.toggle(sessionId)"
    >
        <Star
            class="size-5"
            :class="inPlan ? 'fill-amber-400 text-amber-500' : ''"
        />
    </button>
    <button
        v-else
        type="button"
        class="inline-flex h-11 items-center justify-center gap-2 rounded-xl px-4 text-sm font-medium transition focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
        :class="
            inPlan
                ? 'border border-amber-300 bg-amber-50 text-amber-900 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-100'
                : 'bg-primary text-primary-foreground hover:opacity-90'
        "
        :aria-pressed="inPlan"
        @click="plan.toggle(sessionId)"
    >
        <Star
            class="size-4"
            :class="inPlan ? 'fill-amber-400 text-amber-500' : ''"
        />
        {{ label }}
    </button>
</template>
