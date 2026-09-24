<script setup lang="ts">
import { Share, SquarePlus, X } from '@lucide/vue';
import { useLocalStorage } from '@vueuse/core';
import { computed, onMounted, ref } from 'vue';
import { useI18n } from '@/composables/useI18n';
import { installPrompt, isIos, isStandalone } from '@/lib/pwa';

/**
 * "Add to home screen" hint. Installing matters beyond convenience: on iOS,
 * Web Push (a planned extension) only works for installed web apps.
 */
const { t } = useI18n();
const dismissed = useLocalStorage('agendly:install-hint-dismissed', false);
const ready = ref(false);
const ios = ref(false);

onMounted(() => {
    ios.value = isIos();

    // Give the attendee a moment with the schedule before asking.
    window.setTimeout(() => {
        ready.value = !isStandalone();
    }, 15_000);
});

const visible = computed(
    () =>
        ready.value &&
        !dismissed.value &&
        (installPrompt.value !== null || ios.value),
);

async function install(): Promise<void> {
    const prompt = installPrompt.value;

    if (!prompt) {
        return;
    }

    await prompt.prompt();
    await prompt.userChoice;
    installPrompt.value = null;
    dismissed.value = true;
}
</script>

<template>
    <aside
        v-if="visible"
        class="fixed inset-x-3 bottom-[calc(4.5rem+env(safe-area-inset-bottom))] z-30 mx-auto max-w-md rounded-2xl border border-border bg-card p-4 shadow-lg"
        role="dialog"
        :aria-label="t('public.install.title')"
    >
        <button
            type="button"
            class="absolute top-2 right-2 rounded-full p-1.5 text-muted-foreground hover:bg-accent"
            :aria-label="t('public.install.later')"
            @click="dismissed = true"
        >
            <X class="size-4" />
        </button>
        <div class="flex gap-3 pr-6">
            <img
                src="/icons/icon-192.png"
                alt=""
                class="size-12 shrink-0 rounded-xl"
            />
            <div class="text-sm">
                <p class="font-semibold">{{ t('public.install.title') }}</p>
                <p class="text-muted-foreground">
                    {{ t('public.install.body') }}
                </p>
                <p
                    v-if="!installPrompt && ios"
                    class="mt-2 flex flex-wrap items-center gap-1"
                >
                    <Share class="size-4" />
                    <SquarePlus class="size-4" />
                    {{ t('public.install.ios') }}
                </p>
                <button
                    v-else
                    type="button"
                    class="mt-2 h-9 rounded-xl bg-primary px-4 font-medium text-primary-foreground"
                    @click="install"
                >
                    {{ t('public.install.button') }}
                </button>
            </div>
        </div>
    </aside>
</template>
