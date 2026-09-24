<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Agendly" />
    <main
        class="flex min-h-screen flex-col items-center justify-center gap-6 bg-background p-6 text-center text-foreground"
    >
        <h1 class="text-3xl font-semibold tracking-tight">Agendly</h1>
        <p class="max-w-sm text-muted-foreground">
            Scan the QR code at your event to open its schedule.
        </p>
        <a href="/admin" class="text-sm text-primary underline">
            Organizer panel
        </a>
    </main>
</template>
