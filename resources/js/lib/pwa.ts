import { Workbox } from 'workbox-window';
import { ref } from 'vue';

type BeforeInstallPromptEvent = Event & {
    prompt: () => Promise<void>;
    userChoice: Promise<{ outcome: 'accepted' | 'dismissed' }>;
};

/** Chrome/Android "install" prompt, captured so we can offer it ourselves. */
export const installPrompt = ref<BeforeInstallPromptEvent | null>(null);

export function registerServiceWorker(): void {
    if (!('serviceWorker' in navigator) || !import.meta.env.PROD) {
        return;
    }

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        installPrompt.value = event as BeforeInstallPromptEvent;
    });

    window.addEventListener('appinstalled', () => {
        installPrompt.value = null;
    });

    void new Workbox('/sw.js', { scope: '/' }).register();
}

export function isStandalone(): boolean {
    return (
        window.matchMedia('(display-mode: standalone)').matches ||
        (navigator as Navigator & { standalone?: boolean }).standalone === true
    );
}

export function isIos(): boolean {
    return (
        /iphone|ipad|ipod/i.test(navigator.userAgent) ||
        // iPadOS reports itself as a Mac with touch.
        (navigator.userAgent.includes('Macintosh') &&
            navigator.maxTouchPoints > 1)
    );
}
