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

class GroupsRelationManager extends EventRelationManager
{
    protected static string $relationship = 'groups';

    protected static function translationKey(): string
    {
        return 'admin.group';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label(__('admin.common.name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('leader_name')
                    ->label(__('admin.group.leader_name'))
                    ->maxLength(255),
                TextInput::make('location')
                    ->label(__('admin.group.location'))
                    ->maxLength(255),
                Textarea::make('description')
                    ->label(__('admin.common.description'))
                    ->rows(3)
                    ->columnSpanFull(),
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
                TextColumn::make('leader_name')
                    ->label(__('admin.group.leader_name')),
                TextColumn::make('location')
                    ->label(__('admin.group.location')),
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
