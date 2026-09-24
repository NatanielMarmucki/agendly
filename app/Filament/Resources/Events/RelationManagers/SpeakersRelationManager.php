<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpeakersRelationManager extends EventRelationManager
{
    protected static string $relationship = 'speakers';

    protected static function translationKey(): string
    {
        return 'admin.speaker';
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
                FileUpload::make('photo_path')
                    ->label(__('admin.speaker.photo'))
                    ->image()
                    ->avatar()
                    ->disk('public')
                    ->directory('speakers')
                    ->visibility('public')
                    ->maxSize(2048),
                Textarea::make('bio')
                    ->label(__('admin.speaker.bio'))
                    ->rows(4),
                Repeater::make('links')
                    ->label(__('admin.speaker.links'))
                    ->addActionLabel(__('admin.speaker.add_link'))
                    ->columns(2)
                    ->defaultItems(0)
                    ->schema([
                        TextInput::make('label')
                            ->label(__('admin.speaker.link_label'))
                            ->required()
                            ->maxLength(50),
                        TextInput::make('url')
                            ->label(__('admin.speaker.link_url'))
                            ->required()
                            ->url()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('name')
            ->columns([
                ImageColumn::make('photo_path')
                    ->label(__('admin.speaker.photo'))
                    ->disk('public')
                    ->circular(),
                TextColumn::make('name')
                    ->label(__('admin.common.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sessions_count')
                    ->label(__('admin.speaker.sessions_count'))
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
