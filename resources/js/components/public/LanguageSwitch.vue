<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Languages } from '@lucide/vue';
import { computed } from 'vue';
import { useI18n } from '@/composables/useI18n';

const page = usePage();
const { t, locale } = useI18n();

// A full page load, so every string (and the cached offline copy) switches.
const links = computed(() =>
    page.props.availableLocales.map((code) => {
        const url = new URL(page.url, window.location.origin);
        url.searchParams.set('lang', code);

        return { code, href: `${url.pathname}${url.search}` };
    }),
);
</script>

<template>
    <div
        class="flex items-center gap-1 text-xs"
        :aria-label="t('public.common.language')"
    >
        <Languages class="size-3.5 text-muted-foreground" />
        <a
            v-for="link in links"
            :key="link.code"
            :href="link.href"
            class="rounded px-1.5 py-1 uppercase"
            :class="
                link.code === locale
                    ? 'font-semibold text-foreground'
                    : 'text-muted-foreground'
            "
            :aria-current="link.code === locale ? 'true' : undefined"
            :hreflang="link.code"
        >
            {{ link.code }}
        </a>
    </div>
</template>
