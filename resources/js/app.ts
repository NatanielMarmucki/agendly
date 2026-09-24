import { createInertiaApp } from '@inertiajs/vue3';
import { registerServiceWorker } from '@/lib/pwa';

const appName = import.meta.env.VITE_APP_NAME || 'Agendly';

void createInertiaApp({
    title: (title) => (title ? `${title} · ${appName}` : appName),
    progress: {
        color: '#4f46e5',
    },
});

registerServiceWorker();
