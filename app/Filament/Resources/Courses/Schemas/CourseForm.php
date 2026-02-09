<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // This is written on CREATE only. On EDIT it keeps the existing value.
                Hidden::make('created_by')
                    ->default(fn () => auth()->id())
                    ->dehydrated(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('short_name')
                    ->required(),
                TextInput::make('year')
                    ->required()
                    ->numeric(),
                TextInput::make('semester')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('creator_name')
                    ->label('Created by')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($record) => $record?->creator?->name ?? '—')
                    ->visible(fn ($record) => filled($record)),
            ]);
    }
}
