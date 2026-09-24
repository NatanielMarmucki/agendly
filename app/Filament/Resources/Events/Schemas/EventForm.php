<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Schemas;

use App\Models\Event;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        $timezone = fn (Get $get): string => $get('timezone') ?: Event::DEFAULT_TIMEZONE;

        return $schema
            ->columns(3)
            ->components([
                Section::make(__('admin.event.section_details'))
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('admin.common.name'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation): void {
                                if ($operation === 'create' && blank($get('slug'))) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label(__('admin.event.slug'))
                            ->required()
                            ->maxLength(100)
                            ->alphaDash()
                            ->unique(ignoreRecord: true)
                            ->helperText(fn (?string $state): string => __('admin.event.slug_help', [
                                'url' => url('/e/'.($state ?: '…')),
                            ])),
                        Textarea::make('description')
                            ->label(__('admin.common.description'))
                            ->rows(5)
                            ->columnSpanFull(),
                        FileUpload::make('cover_image_path')
                            ->label(__('admin.event.cover_image'))
                            ->image()
                            ->disk('public')
                            ->directory('events/covers')
                            ->visibility('public')
                            ->maxSize(4096)
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.event.section_when'))
                    ->columnSpan(1)
                    ->schema([
                        Select::make('timezone')
                            ->label(__('admin.event.timezone'))
                            ->options(fn (): array => array_combine(timezone_identifiers_list(), timezone_identifiers_list()))
                            ->searchable()
                            ->required()
                            ->default(Event::DEFAULT_TIMEZONE)
                            ->live(),
                        DateTimePicker::make('starts_at')
                            ->label(__('admin.common.starts_at'))
                            ->required()
                            ->seconds(false)
                            ->timezone($timezone),
                        DateTimePicker::make('ends_at')
                            ->label(__('admin.common.ends_at'))
                            ->required()
                            ->seconds(false)
                            ->timezone($timezone)
                            ->after('starts_at')
                            ->validationMessages(['after' => __('admin.common.ends_after_start')]),
                        TextInput::make('venue')
                            ->label(__('admin.event.venue'))
                            ->maxLength(255),
                    ]),
                Section::make(__('admin.event.section_publishing'))
                    ->columnSpan(1)
                    ->schema([
                        Toggle::make('is_published')
                            ->label(__('admin.event.is_published'))
                            ->helperText(__('admin.event.is_published_help')),
                    ]),
            ]);
    }
}
