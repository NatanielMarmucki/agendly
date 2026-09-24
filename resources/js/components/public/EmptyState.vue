<script setup lang="ts">
defineProps<{ title: string; body?: string }>();
</script>

<template>
    <div
        class="rounded-2xl border border-dashed border-border px-6 py-10 text-center"
    >
        <p class="font-medium">{{ title }}</p>
        <p v-if="body" class="mt-1 text-sm text-muted-foreground">{{ body }}</p>
        <div v-if="$slots.default" class="mt-4">
            <slot />
        </div>
    </div>
</template>
