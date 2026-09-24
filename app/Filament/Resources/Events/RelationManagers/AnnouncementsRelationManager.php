<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\RelationManagers;

use App\Actions\Announcements\PublishAnnouncement;
use App\Enums\AnnouncementPriority;
use App\Models\Announcement;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnouncementsRelationManager extends EventRelationManager
{
    protected static string $relationship = 'announcements';

    protected static function translationKey(): string
    {
        return 'admin.announcement';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->label(__('admin.announcement.title'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->label(__('admin.announcement.body'))
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                ToggleButtons::make('priority')
                    ->label(__('admin.announcement.priority'))
                    ->options(AnnouncementPriority::class)
                    ->default(AnnouncementPriority::Normal)
                    ->inline()
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label(__('admin.announcement.published_at'))
                    ->seconds(false)
                    ->timezone($this->event()->timezone)
                    ->helperText(__('admin.announcement.published_at_help')),
            ]);
    }

    public function table(Table $table): Table
    {
        $timezone = $this->event()->timezone;

        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('published_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin.announcement.title'))
                    ->description(fn (Announcement $record): string => str($record->body)->limit(80)->toString())
                    ->searchable()
                    ->wrap(),
                TextColumn::make('priority')
                    ->label(__('admin.announcement.priority'))
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('admin.announcement.status'))
                    ->badge()
                    ->state(fn (Announcement $record): string => match (true) {
                        $record->published_at === null => __('admin.announcement.status_draft'),
                        $record->published_at->isFuture() => __('admin.announcement.status_scheduled'),
                        default => __('admin.announcement.status_published'),
                    })
                    ->color(fn (Announcement $record): string => match (true) {
                        $record->published_at === null => 'gray',
                        $record->published_at->isFuture() => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('published_at')
                    ->label(__('admin.announcement.published_at'))
                    ->dateTime('j M, H:i', timezone: $timezone)
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('publishNow')
                    ->label(__('admin.announcement.publish_now'))
                    ->icon(Heroicon::OutlinedMegaphone)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Announcement $record): bool => ! $record->isPublished())
                    ->authorize('update')
                    ->action(fn (Announcement $record) => app(PublishAnnouncement::class)->handle($record)),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
