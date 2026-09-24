import { useLocalStorage } from '@vueuse/core';
import { computed, reactive } from 'vue';
import type { AnnouncementSource } from '@/lib/announcementSource';
import type { AnnouncementData, AnnouncementFeedData } from '@/types';

type FeedState = {
    banner: AnnouncementData | null;
    /** Null until the full list has been loaded once. */
    announcements: AnnouncementData[] | null;
    updatedAt: Date | null;
};

const states = reactive(new Map<string, FeedState>());
const subscriptions = new Map<string, { stop: () => void; users: number }>();

function stateFor(slug: string): FeedState {
    if (!states.has(slug)) {
        states.set(slug, {
            banner: null,
            announcements: null,
            updatedAt: null,
        });
    }

    return states.get(slug)!;
}

function applyFeedTo(slug: string, feed: AnnouncementFeedData): void {
    const state = stateFor(slug);
    state.announcements = feed.announcements;
    state.banner = feed.banner;
    state.updatedAt = new Date();
}

/**
 * Shared, live announcement state for one event. The layout keeps the
 * source running; any page can read from it.
 */
export function useAnnouncements(slug: string) {
    const state = computed(() => stateFor(slug));
    const lastSeenId = useLocalStorage<number>(`agendly:seen:${slug}`, 0);
    const dismissedBannerId = useLocalStorage<number>(
        `agendly:dismissed-banner:${slug}`,
        0,
    );

    function applyFeed(feed: AnnouncementFeedData): void {
        applyFeedTo(slug, feed);
    }

    function applyBanner(banner: AnnouncementData | null): void {
        stateFor(slug).banner = banner;
    }

    const newestId = computed(() =>
        Math.max(0, ...(state.value.announcements ?? []).map((a) => a.id)),
    );

    return {
        banner: computed(() => {
            const banner = state.value.banner;

            return banner && banner.id !== dismissedBannerId.value
                ? banner
                : null;
        }),
        announcements: computed(() => state.value.announcements),
        updatedAt: computed(() => state.value.updatedAt),
        hasUnread: computed(() => newestId.value > lastSeenId.value),
        applyFeed,
        applyBanner,
        dismissBanner: () => {
            dismissedBannerId.value = state.value.banner?.id ?? 0;
        },
        markAllSeen: () => {
            lastSeenId.value = Math.max(lastSeenId.value, newestId.value);
        },
    };
}

/**
 * Start receiving updates for an event (idempotent, reference counted).
 */
export function startAnnouncementUpdates(
    slug: string,
    source: AnnouncementSource,
): () => void {
    const existing = subscriptions.get(slug);

    if (existing) {
        existing.users++;
    } else {
        subscriptions.set(slug, {
            stop: source.subscribe((feed) => applyFeedTo(slug, feed)),
            users: 1,
        });
    }

    return () => {
        const subscription = subscriptions.get(slug);

        if (subscription && --subscription.users === 0) {
            subscription.stop();
            subscriptions.delete(slug);
        }
    };
}
