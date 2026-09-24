<?php

declare(strict_types=1);

return [
    'navigation_group' => 'Events',

    'common' => [
        'name' => 'Name',
        'description' => 'Description',
        'starts_at' => 'Starts at',
        'ends_at' => 'Ends at',
        'ends_after_start' => 'The end must be after the start.',
        'timezone_hint' => 'Times are in the event timezone (:timezone).',
    ],

    'event' => [
        'singular' => 'Event',
        'plural' => 'Events',
        'section_details' => 'Details',
        'section_when' => 'Date and venue',
        'section_publishing' => 'Publishing',
        'slug' => 'URL slug',
        'slug_help' => 'Part of the public address: :url',
        'venue' => 'Venue',
        'timezone' => 'Timezone',
        'is_published' => 'Published',
        'is_published_help' => 'Only published events are visible to attendees.',
        'cover_image' => 'Cover image',
        'sessions_count' => 'Sessions',
        'open_public' => 'Open public page',
    ],

    'qr' => [
        'action' => 'Show QR',
        'heading' => 'QR code: :name',
        'description' => 'Print or display this code – attendees scan it with their phone.',
        'download_png' => 'Download PNG',
        'download_svg' => 'Download SVG',
        'unpublished_warning' => 'This event is not published yet – the code will work once it is.',
        'close' => 'Close',
    ],

    'room' => [
        'singular' => 'Room',
        'plural' => 'Rooms',
    ],

    'session' => [
        'singular' => 'Session',
        'plural' => 'Schedule',
        'title' => 'Title',
        'type' => 'Type',
        'room' => 'Room',
        'no_room' => '—',
        'speakers' => 'Speakers',
        'sort_order' => 'Sort order',
        'sort_order_help' => 'Breaks ties between sessions that start at the same time.',
        'time' => 'Time',
        'day' => 'Day',
        'duplicate' => 'Duplicate',
    ],

    'speaker' => [
        'singular' => 'Speaker',
        'plural' => 'Speakers',
        'bio' => 'Bio',
        'photo' => 'Photo',
        'links' => 'Links',
        'link_label' => 'Label',
        'link_url' => 'URL',
        'add_link' => 'Add link',
        'sessions_count' => 'Sessions',
    ],

    'announcement' => [
        'singular' => 'Announcement',
        'plural' => 'Announcements',
        'title' => 'Title',
        'body' => 'Body',
        'priority' => 'Priority',
        'published_at' => 'Published at',
        'published_at_help' => 'Empty = draft. A future date schedules the announcement.',
        'status' => 'Status',
        'status_draft' => 'Draft',
        'status_scheduled' => 'Scheduled',
        'status_published' => 'Published',
        'publish_now' => 'Publish now',
    ],

    'group' => [
        'singular' => 'Group',
        'plural' => 'Groups',
        'leader_name' => 'Leader',
        'location' => 'Meeting place',
    ],
];
