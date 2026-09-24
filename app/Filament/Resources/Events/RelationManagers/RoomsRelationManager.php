<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoomsRelationManager extends EventRelationManager
{
    protected static string $relationship = 'rooms';

    protected static function translationKey(): string
    {
        return 'admin.room';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->label(__('admin.common.name'))
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label(__('admin.common.description'))
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.common.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('admin.common.description'))
                    ->limit(60),
                TextColumn::make('sessions_count')
                    ->label(__('admin.event.sessions_count'))
                    ->counts('sessions'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
