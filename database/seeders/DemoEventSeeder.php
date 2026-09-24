<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AnnouncementPriority;
use App\Enums\SessionType;
use App\Models\Event;
use App\Models\Room;
use App\Models\Session;
use App\Models\Speaker;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * A realistic three-day youth conference, starting today, so the
 * "Now / Next" section has something to show right after seeding.
 */
class DemoEventSeeder extends Seeder
{
    public const string ORGANIZER_EMAIL = 'organizer@agendly.test';

    public const string EVENT_SLUG = 'mlodzi-w-drodze';

    public function run(): void
    {
        $organizer = User::query()->firstOrCreate(
            ['email' => self::ORGANIZER_EMAIL],
            ['name' => 'Organizator Demo', 'password' => 'password', 'email_verified_at' => now()],
        );

        Event::query()->where('slug', self::EVENT_SLUG)->delete();

        $tz = Event::DEFAULT_TIMEZONE;
        $day1 = CarbonImmutable::now($tz)->startOfDay();

        $event = $organizer->events()->create([
            'name' => 'Młodzi w Drodze 2026',
            'slug' => self::EVENT_SLUG,
            'description' => "Trzy dni uwielbienia, konferencji i warsztatów dla młodzieży w wieku 13–19 lat.\n\n"
                .'Zabierz Biblię, śpiwór, strój sportowy i dobry humor. Na miejscu działa punkt informacyjny przy recepcji.',
            'venue' => 'Ośrodek „Wzgórze”, ul. Leśna 12, Wisła',
            'starts_at' => $day1->setTime(14, 0)->utc(),
            'ends_at' => $day1->addDays(2)->setTime(14, 0)->utc(),
            'timezone' => $tz,
            'is_published' => true,
        ]);

        $rooms = [
            'tent' => $event->rooms()->create(['name' => 'Namiot główny', 'description' => 'Duży namiot na łące za ośrodkiem.']),
            'roomB' => $event->rooms()->create(['name' => 'Sala B', 'description' => 'Pierwsze piętro budynku głównego.']),
            'dining' => $event->rooms()->create(['name' => 'Stołówka', 'description' => 'Parter budynku głównego.']),
        ];

        $speakers = [
            'anna' => $event->speakers()->create([
                'name' => 'Anna Kowalczyk',
                'bio' => 'Liderka duszpasterstwa młodzieży w Krakowie, od 10 lat prowadzi obozy i rekolekcje dla nastolatków.',
                'links' => [['label' => 'Instagram', 'url' => 'https://instagram.com/']],
            ]),
            'marek' => $event->speakers()->create([
                'name' => 'ks. Marek Nowak',
                'bio' => 'Duszpasterz akademicki, autor podcastu o wierze i codzienności.',
                'links' => [['label' => 'Podcast', 'url' => 'https://example.com/podcast']],
            ]),
            'kasia' => $event->speakers()->create([
                'name' => 'Katarzyna Wiśniewska',
                'bio' => 'Biblistka i nauczycielka religii. Uczy, jak czytać Pismo Święte z pasją i zrozumieniem.',
                'links' => [],
            ]),
            'tomek' => $event->speakers()->create([
                'name' => 'Tomasz Zieliński',
                'bio' => 'Lider zespołu uwielbienia „Źródło”, muzyk i kompozytor.',
                'links' => [['label' => 'YouTube', 'url' => 'https://youtube.com/']],
            ]),
        ];

        // [day offset, start, minutes, type, title, room key|null, speaker keys, description]
        $schedule = [
            [0, '14:00', 120, SessionType::Other, 'Rejestracja i zakwaterowanie', null, [], 'Recepcja w budynku głównym. Odbierz identyfikator i przydział do pokoju.'],
            [0, '16:30', 30, SessionType::Other, 'Otwarcie konferencji', 'tent', [], null],
            [0, '17:00', 60, SessionType::Worship, 'Uwielbienie', 'tent', ['tomek'], null],
            [0, '18:00', 45, SessionType::Meal, 'Kolacja', 'dining', [], null],
            [0, '19:00', 75, SessionType::Talk, 'Tożsamość: kim jestem naprawdę?', 'tent', ['anna'], 'O tym, skąd bierze się nasza wartość i dlaczego nie definiują nas lajki ani oceny.'],
            [0, '20:30', 60, SessionType::Worship, 'Wieczór uwielbienia i modlitwy', 'tent', ['tomek'], null],
            [1, '08:00', 45, SessionType::Meal, 'Śniadanie', 'dining', [], null],
            [1, '09:00', 30, SessionType::Worship, 'Poranna modlitwa', 'tent', [], null],
            [1, '09:30', 60, SessionType::Talk, 'Przyjaźń, która przetrwa', 'tent', ['marek'], 'Jak budować relacje, które wytrzymują kryzysy, odległość i różnice zdań.'],
            [1, '10:30', 30, SessionType::Break, 'Przerwa kawowa', null, [], null],
            [1, '11:00', 90, SessionType::Workshop, 'Warsztat: Jak czytać Biblię', 'roomB', ['kasia'], 'Praktyczne metody czytania i rozważania Pisma Świętego. Weź ze sobą Biblię i długopis.'],
            [1, '11:00', 90, SessionType::Workshop, 'Warsztat: Muzyka w zespole uwielbienia', 'tent', ['tomek'], 'Dla wszystkich, którzy grają lub śpiewają – i dla tych, którzy chcą zacząć.'],
            [1, '12:30', 60, SessionType::Meal, 'Obiad', 'dining', [], null],
            [1, '14:00', 90, SessionType::Other, 'Małe grupy', null, [], 'Spotkania w małych grupach – sprawdź swoją grupę w zakładce „Grupy”.'],
            [1, '16:00', 60, SessionType::Talk, 'Panel: pytania, które boimy się zadać', 'tent', ['anna', 'marek', 'kasia'], 'Pytania można wrzucać do skrzynki przy recepcji do godziny 14:00.'],
            [1, '17:00', 60, SessionType::Other, 'Czas wolny i sport', null, [], 'Boisko, siatkówka plażowa i gry terenowe.'],
            [1, '18:00', 45, SessionType::Meal, 'Kolacja', 'dining', [], null],
            [1, '19:30', 90, SessionType::Worship, 'Koncert i uwielbienie', 'tent', ['tomek'], null],
            [2, '08:00', 45, SessionType::Meal, 'Śniadanie', 'dining', [], null],
            [2, '09:30', 60, SessionType::Talk, 'Odwaga, by iść dalej', 'tent', ['anna'], 'Co zabieramy do domu i jak nie zgubić tego, co wydarzyło się podczas tych dni.'],
            [2, '10:30', 30, SessionType::Break, 'Przerwa', null, [], null],
            [2, '11:00', 90, SessionType::Worship, 'Nabożeństwo na zakończenie', 'tent', ['marek', 'tomek'], null],
            [2, '12:30', 60, SessionType::Meal, 'Obiad i wyjazd', 'dining', [], null],
        ];

        foreach ($schedule as $index => [$offset, $time, $minutes, $type, $title, $roomKey, $speakerKeys, $description]) {
            [$hour, $minute] = array_map(intval(...), explode(':', $time));
            $startsAt = $day1->addDays($offset)->setTime($hour, $minute);

            /** @var Session $session */
            $session = $event->sessions()->create([
                'room_id' => $roomKey === null ? null : $rooms[$roomKey]->id,
                'title' => $title,
                'description' => $description,
                'starts_at' => $startsAt->utc(),
                'ends_at' => $startsAt->addMinutes($minutes)->utc(),
                'type' => $type,
                'sort_order' => $index,
            ]);

            $session->speakers()->attach(
                array_map(fn (string $key): int => $speakers[$key]->id, $speakerKeys),
            );
        }

        $event->announcements()->createMany([
            [
                'title' => 'Witamy na konferencji!',
                'body' => 'Cieszymy się, że jesteś z nami. Plan dnia znajdziesz w zakładce „Program”, a swój własny plan możesz ułożyć w „Moim planie”.',
                'priority' => AnnouncementPriority::Normal,
                'published_at' => now()->subHours(2),
            ],
            [
                'title' => 'Zgubione i znalezione',
                'body' => 'Znalezione rzeczy oddawaj do recepcji. Czeka tam już niebieska bluza i ładowarka do telefonu.',
                'priority' => AnnouncementPriority::Normal,
                'published_at' => now()->subHour(),
            ],
            [
                'title' => 'Zmiana sali: warsztat biblijny',
                'body' => 'Warsztat „Jak czytać Biblię” odbędzie się w Sali B (pierwsze piętro), a nie w namiocie.',
                'priority' => AnnouncementPriority::Important,
                'published_at' => now()->subMinutes(20),
            ],
        ]);

        $event->groups()->createMany([
            ['name' => 'Grupa 1 – Skała', 'leader_name' => 'Ola i Piotr', 'location' => 'Sala B', 'description' => 'Rocznik 2011–2012.'],
            ['name' => 'Grupa 2 – Źródło', 'leader_name' => 'Magda', 'location' => 'Altana przy boisku', 'description' => 'Rocznik 2009–2010.'],
            ['name' => 'Grupa 3 – Światło', 'leader_name' => 'Kuba', 'location' => 'Świetlica', 'description' => 'Rocznik 2007–2008.'],
            ['name' => 'Grupa 4 – Droga', 'leader_name' => 'Ania i Michał', 'location' => 'Namiot główny (tył)', 'description' => 'Grupa dla wolontariuszy i liderów.'],
        ]);

        // A second, unpublished event shows that drafts stay private.
        Event::query()->where('slug', 'oboz-zimowy-2027')->delete();
        $organizer->events()->create([
            'name' => 'Obóz zimowy 2027',
            'slug' => 'oboz-zimowy-2027',
            'description' => 'Wersja robocza – jeszcze nieopublikowana.',
            'venue' => 'Zakopane',
            'starts_at' => CarbonImmutable::parse('2027-01-25 10:00', $tz)->utc(),
            'ends_at' => CarbonImmutable::parse('2027-01-30 12:00', $tz)->utc(),
            'timezone' => $tz,
            'is_published' => false,
        ]);

        $this->command?->info('Demo organizer: '.self::ORGANIZER_EMAIL.' / password');
        $this->command?->info('Public schedule: '.$event->publicUrl());
    }
}
