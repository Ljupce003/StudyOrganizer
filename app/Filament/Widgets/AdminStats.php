<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Courses', Course::query()->count()),
            Stat::make('Active Courses', Course::query()->where('is_active', true)->count()),
            Stat::make('Users', User::count()),
            Stat::make('Students', User::where('role', UserRole::STUDENT)->count()),
            Stat::make('Professors', User::where('role', UserRole::PROFESSOR)->count()),
            Stat::make('Admins', User::where('role', UserRole::ADMIN)->count()),
        ];
    }
}
