<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import type { SessionType } from '@/types';

const props = defineProps<{ type: SessionType }>();

const { t } = useI18n();

const colors: Record<SessionType, string> = {
    talk: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-200',
    worship:
        'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200',
    workshop:
        'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-200',
    meal: 'bg-stone-100 text-stone-700 dark:bg-stone-500/20 dark:text-stone-300',
    break: 'bg-stone-100 text-stone-700 dark:bg-stone-500/20 dark:text-stone-300',
    other: 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-200',
};

const classes = computed(() => colors[props.type]);
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
        :class="classes"
    >
        {{ t(`enums.session_type.${type}`) }}
    </span>
</template>
