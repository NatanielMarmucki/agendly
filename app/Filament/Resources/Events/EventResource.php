<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Events\RelationManagers\AnnouncementsRelationManager;
use App\Filament\Resources\Events\RelationManagers\GroupsRelationManager;
use App\Filament\Resources\Events\RelationManagers\RoomsRelationManager;
use App\Filament\Resources\Events\RelationManagers\SessionsRelationManager;
use App\Filament\Resources\Events\RelationManagers\SpeakersRelationManager;
use App\Filament\Resources\Events\Schemas\EventForm;
use App\Filament\Resources\Events\Tables\EventsTable;
use App\Models\Event;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('admin.event.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.event.plural');
    }

    /**
     * Organizers only ever see their own events.
     *
     * @return Builder<Event>
     */
    public static function getEloquentQuery(): Builder
    {
        /** @var User|null $user */
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->when(
                $user !== null,
                fn (Builder $query) => $query->whereBelongsTo($user),
                fn (Builder $query) => $query->whereRaw('1 = 0'),
            );
    }

    public static function form(Schema $schema): Schema
    {
        return EventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SessionsRelationManager::class,
            RoomsRelationManager::class,
            SpeakersRelationManager::class,
            AnnouncementsRelationManager::class,
            GroupsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}
