<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Tables;

use App\Filament\Resources\Events\Actions\ShowQrCodeAction;
use App\Models\Event;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->withCount('sessions'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.common.name'))
                    ->description(fn (Event $record): ?string => $record->venue)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label(__('admin.common.starts_at'))
                    ->dateTime('j M Y, H:i', timezone: fn (Event $record): string => $record->timezone)
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label(__('admin.common.ends_at'))
                    ->dateTime('j M Y, H:i', timezone: fn (Event $record): string => $record->timezone)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sessions_count')
                    ->label(__('admin.event.sessions_count'))
                    ->numeric(),
                IconColumn::make('is_published')
                    ->label(__('admin.event.is_published'))
                    ->boolean(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ShowQrCodeAction::make(),
                ]),
            ]);
    }
}
