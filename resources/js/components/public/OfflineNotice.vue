<script setup lang="ts">
import { WifiOff } from '@lucide/vue';
import { useOnline } from '@vueuse/core';
import { useI18n } from '@/composables/useI18n';

const online = useOnline();
const { t } = useI18n();
</script>

<template>
    <div
        v-if="!online"
        role="status"
        class="flex items-center justify-center gap-2 bg-stone-800 px-4 py-1.5 text-xs text-white"
    >
        <WifiOff class="size-3.5" />
        {{ t('public.common.offline') }}
    </div>
</template>
