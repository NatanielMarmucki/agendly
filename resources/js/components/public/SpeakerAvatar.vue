<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        name: string;
        photoUrl: string | null;
        size?: 'sm' | 'lg';
    }>(),
    { size: 'sm' },
);

const initials = computed(() =>
    props.name
        .split(/\s+/)
        .filter((part) => part.length > 0 && part[0] === part[0].toUpperCase())
        .slice(-2)
        .map((part) => part[0])
        .join(''),
);
</script>

<template>
    <img
        v-if="photoUrl"
        :src="photoUrl"
        :alt="name"
        loading="lazy"
        class="shrink-0 rounded-full object-cover"
        :class="size === 'lg' ? 'size-16' : 'size-10'"
    />
    <div
        v-else
        aria-hidden="true"
        class="flex shrink-0 items-center justify-center rounded-full bg-secondary font-semibold text-secondary-foreground"
        :class="size === 'lg' ? 'size-16 text-lg' : 'size-10 text-sm'"
    >
        {{ initials }}
    </div>
</template>
