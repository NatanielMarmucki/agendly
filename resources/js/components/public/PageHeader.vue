<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{ title: string; subtitle?: string | null }>();
</script>

<template>
    <Head :title="title" />
    <div class="mb-4">
        <slot name="before" />
        <h1 class="text-2xl font-semibold tracking-tight">{{ title }}</h1>
        <p v-if="subtitle" class="mt-1 text-sm text-muted-foreground">
            {{ subtitle }}
        </p>
    </div>
</template>
