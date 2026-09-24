/**
 * Mirrors of the PHP DTOs in app/Data/Public. Keep both sides in sync.
 *
 * All timestamps are ISO 8601 strings carrying the event's UTC offset.
 */

export type SessionType =
    'talk' | 'worship' | 'workshop' | 'meal' | 'break' | 'other';

export type AnnouncementPriority = 'normal' | 'important';

export type EventData = {
    slug: string;
    name: string;
    description: string | null;
    venue: string | null;
    timezone: string;
    startsAt: string;
    endsAt: string;
    /** Local calendar days (YYYY-MM-DD) in the event timezone. */
    days: string[];
    coverImageUrl: string | null;
};

export type RoomData = {
    id: number;
    name: string;
    description: string | null;
};

export type SpeakerSummaryData = {
    id: number;
    name: string;
    photoUrl: string | null;
};

export type SessionData = {
    id: number;
    title: string;
    description: string | null;
    type: SessionType;
    /** False for meals and breaks. */
    plannable: boolean;
    startsAt: string;
    endsAt: string;
    /** Local day (YYYY-MM-DD) the session starts on. */
    day: string;
    room: RoomData | null;
    speakers: SpeakerSummaryData[];
};

export type SpeakerLink = {
    label: string;
    url: string;
};

export type SpeakerData = {
    id: number;
    name: string;
    bio: string | null;
    photoUrl: string | null;
    links: SpeakerLink[];
    sessions: { id: number; title: string }[];
};

export type AnnouncementData = {
    id: number;
    title: string;
    body: string;
    priority: AnnouncementPriority;
    publishedAt: string;
};

export type AnnouncementFeedData = {
    /** Newest first. */
    announcements: AnnouncementData[];
    banner: AnnouncementData | null;
};

export type GroupData = {
    id: number;
    name: string;
    leaderName: string | null;
    location: string | null;
    description: string | null;
};

export type ScheduleData = {
    sessions: SessionData[];
    rooms: RoomData[];
};

/** Props shared by every public event page (SharePublicEventProps). */
export type PublicPageProps = {
    event: EventData;
    banner: AnnouncementData | null;
};
