import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        inertia(),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        wayfinder({
            formVariants: true,
        }),
        VitePWA({
            // Registered manually in resources/js/lib/pwa.ts.
            injectRegister: false,
            registerType: 'autoUpdate',
            // Built into public/build/sw.js and served at /sw.js by Laravel
            // (see routes/public.php) so it controls the whole site.
            filename: 'sw.js',
            // The web app manifest is a static file: public/manifest.webmanifest.
            manifest: false,
            workbox: {
                // One self-contained file: served from "/" it cannot load
                // workbox chunks that live next to it in /build.
                inlineWorkboxRuntime: true,
                globPatterns: ['**/*.{js,css,svg,png,woff2}'],
                // Precache URLs are relative to /build, but the SW lives at /sw.js.
                manifestTransforms: [
                    async (entries) => ({
                        manifest: entries.map((entry) => ({
                            ...entry,
                            url: entry.url.startsWith('/')
                                ? entry.url
                                : `/build/${entry.url}`,
                        })),
                        warnings: [],
                    }),
                ],
                navigateFallback: null,
                cleanupOutdatedCaches: true,
                clientsClaim: true,
                skipWaiting: true,
                runtimeCaching: [
                    {
                        // Event pages (HTML and Inertia JSON), announcements
                        // feed and .ics files: fresh when online, cached copy
                        // when the venue Wi-Fi is gone.
                        urlPattern: ({ url, sameOrigin }) =>
                            sameOrigin &&
                            (url.pathname === '/' ||
                                url.pathname.startsWith('/e/')),
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'agendly-events',
                            networkTimeoutSeconds: 4,
                            expiration: {
                                maxEntries: 400,
                                maxAgeSeconds: 60 * 60 * 24 * 30,
                            },
                            cacheableResponse: { statuses: [200] },
                            plugins: [
                                {
                                    // The same URL answers with HTML or with
                                    // Inertia JSON: keep both in the cache.
                                    cacheKeyWillBeUsed: async ({ request }) => {
                                        if (!request.headers.get('X-Inertia')) {
                                            return request.url;
                                        }

                                        const url = new URL(request.url);
                                        url.searchParams.set('__inertia', '1');

                                        return url.href;
                                    },
                                },
                            ],
                        },
                    },
                    {
                        urlPattern: ({ url, sameOrigin }) =>
                            sameOrigin && url.pathname.startsWith('/storage/'),
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'agendly-media',
                            expiration: {
                                maxEntries: 200,
                                maxAgeSeconds: 60 * 60 * 24 * 30,
                            },
                            cacheableResponse: { statuses: [0, 200] },
                        },
                    },
                ],
            },
        }),
    ],
    server: {
        watch: {
            ignored: ['**/.claude/**', '**/vendor/**'],
        },
    },
});
