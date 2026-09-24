<?php

declare(strict_types=1);

return [
    'navigation_group' => 'Wydarzenia',

    'common' => [
        'name' => 'Nazwa',
        'description' => 'Opis',
        'starts_at' => 'Początek',
        'ends_at' => 'Koniec',
        'ends_after_start' => 'Koniec musi być po początku.',
        'timezone_hint' => 'Godziny w strefie czasowej wydarzenia (:timezone).',
    ],

    'event' => [
        'singular' => 'Wydarzenie',
        'plural' => 'Wydarzenia',
        'section_details' => 'Szczegóły',
        'section_when' => 'Termin i miejsce',
        'section_publishing' => 'Publikacja',
        'slug' => 'Adres (slug)',
        'slug_help' => 'Część publicznego adresu: :url',
        'venue' => 'Miejsce',
        'timezone' => 'Strefa czasowa',
        'is_published' => 'Opublikowane',
        'is_published_help' => 'Tylko opublikowane wydarzenia są widoczne dla uczestników.',
        'cover_image' => 'Zdjęcie w tle',
        'sessions_count' => 'Punkty programu',
        'open_public' => 'Otwórz stronę',
    ],

    'qr' => [
        'action' => 'Pokaż kod QR',
        'heading' => 'Kod QR: :name',
        'description' => 'Wydrukuj lub wyświetl ten kod – uczestnicy zeskanują go telefonem.',
        'download_png' => 'Pobierz PNG',
        'download_svg' => 'Pobierz SVG',
        'unpublished_warning' => 'To wydarzenie nie jest jeszcze opublikowane – kod zadziała dopiero po publikacji.',
        'close' => 'Zamknij',
    ],

    'room' => [
        'singular' => 'Sala',
        'plural' => 'Sale',
    ],

    'session' => [
        'singular' => 'Punkt programu',
        'plural' => 'Program',
        'title' => 'Tytuł',
        'type' => 'Rodzaj',
        'room' => 'Sala',
        'no_room' => '—',
        'speakers' => 'Prowadzący',
        'sort_order' => 'Kolejność',
        'sort_order_help' => 'Rozstrzyga kolejność punktów o tej samej godzinie.',
        'time' => 'Godzina',
        'day' => 'Dzień',
        'duplicate' => 'Duplikuj',
    ],

    'speaker' => [
        'singular' => 'Prowadzący',
        'plural' => 'Prowadzący',
        'bio' => 'Bio',
        'photo' => 'Zdjęcie',
        'links' => 'Linki',
        'link_label' => 'Etykieta',
        'link_url' => 'Adres URL',
        'add_link' => 'Dodaj link',
        'sessions_count' => 'Punkty programu',
    ],

    'announcement' => [
        'singular' => 'Ogłoszenie',
        'plural' => 'Ogłoszenia',
        'title' => 'Tytuł',
        'body' => 'Treść',
        'priority' => 'Priorytet',
        'published_at' => 'Data publikacji',
        'published_at_help' => 'Puste = szkic. Data w przyszłości = publikacja zaplanowana.',
        'status' => 'Status',
        'status_draft' => 'Szkic',
        'status_scheduled' => 'Zaplanowane',
        'status_published' => 'Opublikowane',
        'publish_now' => 'Opublikuj teraz',
    ],

    'group' => [
        'singular' => 'Grupa',
        'plural' => 'Grupy',
        'leader_name' => 'Prowadzący grupę',
        'location' => 'Miejsce spotkań',
    ],
];
