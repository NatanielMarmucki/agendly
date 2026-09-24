<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\RelationManagers;

use App\Enums\SessionType;
use App\Models\Session;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class SessionsRelationManager extends EventRelationManager
{
    protected static string $relationship = 'sessions';

    protected static function translationKey(): string
    {
        return 'admin.session';
    }

    public function form(Schema $schema): Schema
    {
        $event = $this->event();

        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->label(__('admin.session.title'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('type')
                    ->label(__('admin.session.type'))
                    ->options(SessionType::class)
                    ->default(SessionType::Talk)
                    ->required(),
                Select::make('room_id')
                    ->label(__('admin.session.room'))
                    ->relationship('room', 'name', fn (Builder $query) => $query->whereBelongsTo($event))
                    ->rule(Rule::exists('rooms', 'id')->where('event_id', $event->id))
                    ->preload(),
                DateTimePicker::make('starts_at')
                    ->label(__('admin.common.starts_at'))
                    ->required()
                    ->seconds(false)
                    ->timezone($event->timezone)
                    ->default($event->starts_at)
                    ->helperText($this->timezoneHint()),
                DateTimePicker::make('ends_at')
                    ->label(__('admin.common.ends_at'))
                    ->required()
                    ->seconds(false)
                    ->timezone($event->timezone)
                    ->after('starts_at')
                    ->validationMessages(['after' => __('admin.common.ends_after_start')]),
                Select::make('speakers')
                    ->label(__('admin.session.speakers'))
                    ->relationship('speakers', 'name', fn (Builder $query) => $query->whereBelongsTo($event))
                    ->nestedRecursiveRules([Rule::exists('speakers', 'id')->where('event_id', $event->id)])
                    ->multiple()
                    ->preload()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label(__('admin.common.description'))
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label(__('admin.session.sort_order'))
                    ->helperText(__('admin.session.sort_order_help'))
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        $timezone = $this->event()->timezone;

        return $table
            ->recordTitleAttribute('title')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['room', 'speakers']))
            ->defaultSort(fn (Builder $query) => $query->orderBy('starts_at')->orderBy('sort_order'))
            ->defaultGroup(
                Group::make('starts_at')
                    ->label(__('admin.session.day'))
                    ->getKeyFromRecordUsing(fn (Session $record): string => $record->starts_at->setTimezone($timezone)->toDateString())
                    ->getTitleFromRecordUsing(fn (Session $record): string => $record->starts_at->setTimezone($timezone)->translatedFormat('l, j F'))
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('starts_at', $direction))
                    ->collapsible(),
            )
            ->groupingSettingsHidden()
            ->columns([
                TextColumn::make('starts_at')
                    ->label(__('admin.session.time'))
                    ->formatStateUsing(fn (Session $record): string => $record->starts_at->setTimezone($timezone)->format('H:i')
                        .'–'.$record->ends_at->setTimezone($timezone)->format('H:i'))
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('admin.session.title'))
                    ->searchable()
                    ->wrap(),
                TextColumn::make('type')
                    ->label(__('admin.session.type'))
                    ->badge(),
                TextColumn::make('room.name')
                    ->label(__('admin.session.room'))
                    ->placeholder(__('admin.session.no_room')),
                TextColumn::make('speakers.name')
                    ->label(__('admin.session.speakers'))
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('admin.session.type'))
                    ->options(SessionType::class),
                SelectFilter::make('room')
                    ->label(__('admin.session.room'))
                    ->relationship('room', 'name', fn (Builder $query) => $query->whereBelongsTo($this->event())),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                ReplicateAction::make()
                    ->label(__('admin.session.duplicate'))
                    ->after(fn (Session $replica, Session $record) => $replica->speakers()->sync($record->speakers->modelKeys())),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
