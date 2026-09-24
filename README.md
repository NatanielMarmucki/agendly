# Agendly

A lightweight schedule app for small events (conferences, youth church
conferences, camps, meetups — 50–500 attendees).

- **Attendees** scan a QR code and open the event in the browser: no install,
  no account, no personal data. They browse the schedule, see what is on
  _now_ and _next_, build a personal plan (stored on their phone only), add
  sessions to their calendar and read announcements. After the first visit
  the whole event works offline, and it can be added to the home screen as
  a PWA.
- **Organizers** manage events, rooms, sessions, speakers, announcements and
  small groups in a Filament panel at `/admin`, and print the event QR code.

UI is in Polish by default with English as the second language.

---

## Quick start

Requirements: PHP 8.3+ (with `pdo_sqlite`, `gd`, `intl`), Composer 2, Node 22+.

```bash
composer setup              # install deps, .env, key, SQLite db, migrate, build assets
php artisan migrate --seed  # demo data (safe to re-run: the demo event is recreated)
composer dev                # app server + Vite
```

Then open:

| What                      | URL                                     |
| ------------------------- | --------------------------------------- |
| Attendee app (demo event) | http://localhost:8000/e/mlodzi-w-drodze |
| Organizer panel           | http://localhost:8000/admin             |

**Demo organizer:** `organizer@agendly.test` / `password`

The demo event, _Młodzi w Drodze 2026_, is a three-day youth conference that
**starts on the day you seed it**, so the “Now / Next” section has content.
It includes 23 sessions, 3 rooms, 4 speakers, 3 announcements (one
important) and 4 groups. A second event, _Obóz zimowy 2027_, is left
unpublished, so its public URL returns 404.

New organizer accounts: `php artisan make:filament-user`.

### Quality checks

```bash
composer test        # pint --test, larastan (level 7), pest
composer lint        # pint (fix)
npm run lint         # eslint + prettier --check
npm run lint:fix
npm run types:check  # vue-tsc
npm run build
```

---

## Stack

Laravel 13 (PHP 8.3+, `declare(strict_types=1)` everywhere) · official Vue
starter kit (Inertia 3 + Vue 3 + TypeScript + Tailwind 4 + Vite) · Filament 5 ·
Wayfinder (typed routes in TS) · `vite-plugin-pwa` · `spatie/icalendar-generator` ·
`simplesoftwareio/simple-qrcode` · Pest 4 · Larastan · Pint · ESLint + Prettier.

SQLite by default. Nothing is SQLite-specific: switch `DB_CONNECTION` to
`mysql` or `pgsql`.

Changes from the stock starter kit:

- **Fortify and the kit's dashboard, settings and auth pages were removed.**
  Organizers sign in through Filament; attendees never sign in. This leaves
  one login, not two.
- `vite-plus` (oxlint/oxfmt) was replaced with plain Vite + ESLint +
  Prettier, as the brief requires.
- Remote Bunny fonts were replaced by the system font stack: no
  third-party requests, and it works offline.

---

## Architecture

```
app/
  Actions/
    Announcements/PublishAnnouncement.php
    Public/            BuildSchedule, ShowSession, ListSpeakers, ListGroups,
                       BuildAnnouncementFeed   ← query logic for the public app
  Data/Public/         Typed Inertia props (EventData, SessionData, …)
  Enums/               SessionType, AnnouncementPriority
  Events/              AnnouncementPublished   ← extension hook (push / Reverb)
  Filament/            EventResource + relation managers, QR action, avatars
  Http/
    Controllers/Public Thin controllers: action → DTO → Inertia::render
    Controllers/Admin  QR PNG/SVG download
    Middleware/        EnsureEventIsPublished, SharePublicEventProps,
                       SetLocale, PublicResponseHeaders, HandleInertiaRequests
    Requests/Public/   EventCalendarRequest (?sessions= filter)
  Models/              Event, Room, Session (table event_sessions), Speaker,
                       Announcement, Group, User (+ BelongsToEvent concern)
  Policies/            EventPolicy, EventContentPolicy (+ one per model)
  Services/            QrCodeGenerator, Calendar/IcsCalendarBuilder
lang/{pl,en}/          public.php (attendee UI), admin.php, enums.php
resources/js/
  pages/public/        Schedule, SessionShow, Plan, Announcements,
                       Speakers, Groups, Info
  layouts/PublicLayout.vue   persistent layout: header, banner, bottom nav
  components/public/   SessionCard, PlanToggle, AnnouncementBanner,
                       InstallHint, …
  composables/         useI18n, useEventTime, usePlan, useAnnouncements, useNow
  lib/                 announcementSource (polling), pwa, offlineWarmup
  types/public.ts      TS mirrors of app/Data/Public
routes/public.php      attendee routes (own middleware group)
routes/web.php         organizer helpers next to Filament
```

### Request flow (attendee)

`/e/{slug}/…` routes run in a separate, lean **`public` middleware group**
(see `bootstrap/app.php`). It has no session, no CSRF token and sets no
cookies, except an optional language cookie. Pages are sent with
`X-Robots-Tag: noindex`.

1. `{event:slug}` is bound and `EnsureEventIsPublished` returns 404 for drafts.
2. `SharePublicEventProps` shares `event` and the current `banner`.
3. The controller calls one action, which returns DTOs from `app/Data/Public`.
4. Inertia renders a Vue page whose props are typed by `resources/js/types/public.ts`.

Nested bindings are scoped (`scopeBindings()`), so a session ID from another
event returns 404.

### Time zones

- Datetimes are stored in **UTC**. Each event has a `timezone` (default
  `Europe/Warsaw`).
- Filament date pickers work in the event's timezone and convert on save.
- DTOs send ISO 8601 strings **with the event's offset**
  (`2026-07-02T19:00:00+02:00`) plus a precomputed local `day`. The client
  can compare instants for Now/Next and format times with
  `Intl.DateTimeFormat({ timeZone: event.timezone })`. Times always show
  in event time, whatever the phone's timezone.
- `.ics` files write `DTSTART;TZID=Europe/Warsaw:…` with a matching
  `VTIMEZONE`, so they stay correct across DST changes.

### Authorization

- `EventResource::getEloquentQuery()` limits every panel query to the
  signed-in organizer's events. Another organizer's event returns 404.
- Policies: `EventPolicy` checks ownership. `EventContentPolicy` gives
  rooms, sessions, speakers, announcements and groups the same ownership
  rule as their event. The QR download route uses `can:view,event`.
- Room and speaker selects on a session are validated to belong to the
  same event.

### Announcements

- A draft has `published_at = NULL`; a future date schedules the
  announcement.
- The latest published **important** announcement is shared as a banner on
  every page. Attendees can dismiss it on their device.
- The client polls `GET /e/{slug}/announcements.json` every 60 s. Polling
  pauses while the tab is hidden or offline and refreshes when the tab
  becomes visible again. Polling sits behind the `AnnouncementSource`
  interface (`resources/js/lib/announcementSource.ts`). To move to
  **Laravel Reverb**, implement that interface with Echo and broadcast from
  the existing `AnnouncementPublished` event. No page code changes.

### PWA and offline

- `vite-plugin-pwa` (`generateSW`) builds `public/build/sw.js`. Laravel
  serves it at **`/sw.js`** so its scope covers the whole site. The web
  manifest is a static `public/manifest.webmanifest` (placeholder icons in
  `public/icons/`).
- Precache: every built JS/CSS asset, including all lazily loaded pages.
- Runtime cache (`NetworkFirst`, 4 s timeout): `/` and everything under
  `/e/` (HTML pages, Inertia JSON, the announcements feed, `.ics`). HTML
  and Inertia JSON for the same URL get separate cache keys. Uploaded
  media under `/storage/` uses `CacheFirst`.
- **Warm-up:** a few seconds after the first visit,
  `lib/offlineWarmup.ts` fetches every page of the event, including each
  session, as both HTML and Inertia JSON. The schedule then works fully
  offline, even pages never opened, and even on a cold start from the home
  screen. It runs once per event and build version, one request at a time,
  to spare busy venue Wi-Fi.
- The installed app starts at `/`. The landing page reopens the last event
  (stored in `localStorage`).
- The `InstallHint` component shows the native install prompt on
  Android/Chrome and “Share → Add to Home Screen” steps on iOS. Installing
  is a prerequisite for Web Push on iOS.

### Privacy

- No attendee accounts, registration, contact exchange, chat or analytics.
- “My plan”, the dismissed banner, the last-seen announcement and the last
  opened event are stored in `localStorage` on the device only. The plan
  export (`calendar.ics?sessions=1,2,3`) sends only session IDs, and
  nothing is stored.
- Public responses set no session or CSRF cookies. The only cookie is
  `agendly_locale`, and only after the visitor switches language.
- No third-party requests from the attendee app: fonts are local and the
  map link opens only on tap. The Filament panel uses local initials
  avatars instead of ui-avatars.com.

### i18n

- Laravel lang files `lang/{pl,en}/public.php`, `enums.php` and `admin.php`.
  Default locale `pl`, fallback `en`.
- The attendee strings (`public.*`, `enums.*`) are shared with Vue as
  flattened keys. `useI18n().t('public.plan.title', { … })` works like
  `__()`.
- `?lang=en` switches language and is remembered in `agendly_locale`.

---

## Organizer panel

`/admin` (Filament 5):

- **Events:** CRUD, only your own. Slug auto-fills from the name. Timezone,
  cover image, publish toggle, “Open public page”.
- **Show QR:** modal preview plus **PNG** and **SVG** download of the public
  URL. PNG uses Imagick when available, otherwise a GD fallback built on
  the same bacon/bacon-qr-code matrix.
- Relation managers on the event (tabs):
    - **Program (sessions):** grouped by local day, sorted by time,
      filterable by type and room. Duplicate action keeps speakers.
    - **Rooms**, **Speakers** (photo, bio, links), **Groups**.
    - **Announcements:** draft/scheduled/published status and “Publish now”.

---

## Tests

Pest feature tests in `tests/Feature`:

- `Public/PublicRoutesTest`: every public page for published, unpublished
  and unknown events; timezone-aware props; scoped sessions; banner; no
  cookies; language switch.
- `Public/AnnouncementFeedTest`: JSON feed contents, ordering, banner,
  drafts and scheduled announcements hidden.
- `Public/CalendarExportTest`: `.ics` for a session, the whole event and a
  plan subset; TZID/VTIMEZONE output; summer and winter offsets;
  validation; 404s.
- `Admin/EventResourceTest`: Filament scoping and authorization, create
  flow (owner and timezone conversion), relation manager scoping,
  cross-event room rejected, publish now.
- `Admin/EventQrCodeTest`: PNG/SVG download, forbidden for other
  organizers, guest redirect.
- `Domain/*`: event days in the event timezone, enum casts, published
  scope, policies for every model.

---

## Deploying to a single VPS (Forge or Coolify)

One app, one deploy. There is no separate API, queue workers are optional,
and SQLite is fine at this scale.

1. **Server:** PHP 8.3+ with `pdo_sqlite` (or `pdo_mysql`/`pdo_pgsql`),
   `gd`, `intl`, `mbstring`; optionally `imagick`. Node 22 is needed for
   the build step only.
2. **Environment:**
    ```dotenv
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://agendly.example.org   # used in QR codes and .ics links
    APP_LOCALE=pl
    DB_CONNECTION=sqlite                  # or mysql / pgsql
    SESSION_DRIVER=database               # used by the organizer panel only
    ```
3. **Deploy script** (Forge “Deploy Script” / Coolify “Build + Post-deploy”):
    ```bash
    composer install --no-dev --optimize-autoloader
    npm ci && npm run build
    php artisan migrate --force
    php artisan storage:link
    php artisan optimize
    php artisan filament:optimize
    ```
4. **HTTPS is required.** Service workers and install prompts only work on
   secure origins. Forge and Coolify both provide Let's Encrypt.
5. **Web server:** serve `public/`. `/sw.js` is served by Laravel with
   `Cache-Control: no-cache`, so don't add a static rule that caches it.
   You can cache `/build/assets/*` for a long time (hashed file names).
6. **SQLite:** keep `database/database.sqlite` on a persistent volume
   (Coolify) and include it in backups.
7. Create the first organizer: `php artisan make:filament-user`.

Load: 500 attendees polling every 60 s is about 8 small requests per
second, with no session writes (public routes have no session). A $5 VPS
handles it.

---

## Extension points (not implemented)

| Feature                            | Where to plug in                                                                                                                                                                                                                             |
| ---------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Web Push**                       | Listen to `App\Events\AnnouncementPublished`; store push subscriptions per event (anonymous endpoint + keys only); switch the PWA to `injectManifest` to add a `push` handler. `InstallHint` already covers the iOS home-screen requirement. |
| **Laravel Reverb**                 | Implement `AnnouncementSource` with Echo, broadcast `AnnouncementPublished`, pass the new source to `startAnnouncementUpdates()` in `PublicLayout.vue`.                                                                                      |
| **Scheduled announcements → push** | A scheduled command that dispatches `AnnouncementPublished` when `published_at` passes (the model only fires it on save).                                                                                                                    |
| **Q&A / polls, session ratings**   | New models under an `Event`/`Session` with `BelongsToEvent`, relation managers on `EventResource`, a `SessionData` field, and a section in `SessionShow.vue`. Keep them anonymous.                                                           |
| **Song lyrics / setlists**         | A `Setlist`/`Song` model related to `Session` (worship sessions), shown on the session page. It fits the offline cache as is.                                                                                                                |
| **Multi-organizer teams, billing** | Replace `user_id` ownership with a team relation. `EventPolicy`, `EventContentPolicy` and `EventResource::getEloquentQuery()` are the only places ownership is checked. Filament tenancy is an option.                                       |

---

## Known limitations and next steps

- PWA icons are placeholders (`public/icons/`). Replace them with real
  artwork.
- An offline navigation to a URL outside a cached event gets the browser's
  offline page (no custom fallback yet).
- The announcement feed is not cached server-side. Add a short cache if an
  event grows well beyond 500 attendees.
