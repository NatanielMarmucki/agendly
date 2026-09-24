<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { lastEvent } from '@/lib/lastEvent';
import publicRoutes from '@/routes/public';

const recentEvent = ref<string | null>(null);

onMounted(() => {
    recentEvent.value = lastEvent();

    // Launched from the home screen: go straight back to the event.
    const standalone = window.matchMedia('(display-mode: standalone)').matches;

    if (standalone && recentEvent.value) {
        router.visit(publicRoutes.schedule(recentEvent.value), {
            replace: true,
        });
    }
});
</script>

<template>
    <Head title="Agendly" />
    <main
        class="flex min-h-dvh flex-col items-center justify-center gap-6 bg-background p-6 text-center text-foreground"
    >
        <h1 class="text-3xl font-semibold tracking-tight">Agendly</h1>
        <p class="max-w-sm text-muted-foreground">
            Zeskanuj kod QR na wydarzeniu, aby otworzyć program.
            <br />
            <span lang="en"
                >Scan the QR code at your event to open its schedule.</span
            >
        </p>
        <Link
            v-if="recentEvent"
            :href="publicRoutes.schedule(recentEvent)"
            class="inline-flex h-11 items-center rounded-xl bg-primary px-5 font-medium text-primary-foreground"
        >
            Wróć do wydarzenia · Back to your event
        </Link>
        <a href="/admin" class="text-sm text-muted-foreground underline">
            Panel organizatora · Organizer panel
        </a>
    </main>
</template>
