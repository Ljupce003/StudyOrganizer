<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state)) // only saves if filled
                    ->required(fn ($livewire) => $livewire instanceof CreateUser) // only required on create
                    ->hidden(fn ($livewire) => $livewire instanceof EditUser) // hide on edit
                    ->minLength(8)
                    ->maxLength(255),

                Select::make('role')
                    ->options(UserRole::class)
                    ->default('student')
                    ->required(),
            ]);
    }
}
