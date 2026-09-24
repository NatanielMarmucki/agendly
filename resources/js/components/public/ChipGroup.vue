<script setup lang="ts" generic="T extends string | number">
defineProps<{
    label: string;
    options: { value: T | null; label: string }[];
}>();

const model = defineModel<T | null>({ required: true });
</script>

<template>
    <div role="group" :aria-label="label" class="-mx-4 overflow-x-auto px-4">
        <div class="flex w-max gap-2">
            <button
                v-for="option in options"
                :key="String(option.value)"
                type="button"
                class="h-9 rounded-full border px-3 text-sm whitespace-nowrap transition focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                :class="
                    model === option.value
                        ? 'border-primary bg-primary text-primary-foreground'
                        : 'border-border bg-card hover:bg-accent'
                "
                :aria-pressed="model === option.value"
                @click="model = option.value"
            >
                {{ option.label }}
            </button>
        </div>
    </div>
</template>
