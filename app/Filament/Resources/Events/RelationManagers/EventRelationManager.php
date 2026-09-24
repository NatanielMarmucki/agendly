<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\RelationManagers;

use App\Models\Event;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Model;

/**
 * Base for everything managed inside an event. Records are always created
 * through the owner relationship, so they inherit the event automatically.
 */
abstract class EventRelationManager extends RelationManager
{
    /**
     * Translation namespace, e.g. "admin.room".
     */
    abstract protected static function translationKey(): string;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(static::translationKey().'.plural');
    }

    public static function getModelLabel(): string
    {
        return __(static::translationKey().'.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __(static::translationKey().'.plural');
    }

    protected function event(): Event
    {
        $event = $this->getOwnerRecord();
        assert($event instanceof Event);

        return $event;
    }

    protected function timezoneHint(): string
    {
        return __('admin.common.timezone_hint', ['timezone' => $this->event()->timezone]);
    }
}
