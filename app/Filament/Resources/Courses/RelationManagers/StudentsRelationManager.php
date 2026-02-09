<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Enums\StudentCourseStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    protected static ?string $title = 'Students';
    protected static ?string $label = 'Student';
    protected static ?string $pluralLabel = 'Students';

    protected static ?string $relatedResource = UserResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),

                TextColumn::make('pivot.status')
                    ->label('Status')
                    ->sortable(),

                TextColumn::make('pivot.enrolled_at')
                    ->label('Enrolled at')
                    ->date()
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('enrollStudent')
                    ->label('Enroll student')
                    ->schema([
                        TextInput::make('course')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn () =>
                                $this->getOwnerRecord()->code
                                . ' — '
                                . $this->getOwnerRecord()->name
                            ),

                        Select::make('user_id')
                            ->label('Student')
                            ->searchable()
                            ->options(fn () => User::query()
                                ->where('role', UserRole::STUDENT)
                                ->whereDoesntHave('Courses', fn ($q) =>
                                        $q->where('courses.id', $this->getOwnerRecord()->id))
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(fn ($u) => [
                                    $u->id => "$u->name - $u->email"
                                ])
                            )
                            ->required(),

                        Select::make('status')
                            ->options(StudentCourseStatus::class)
                            ->required(),

                        DatePicker::make('enrolled_at')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $this->getOwnerRecord()
                            ->students()
                            ->attach($data['user_id'], [
                                'status' => $data['status'],
                                'enrolled_at' => $data['enrolled_at'],
                            ]);
                    })
            ])
            ->recordActions([
                Action::make('editEnrollment')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->fillForm(fn (Model $record) => [
                        'status' => $record->pivot?->status,
                        'enrolled_at' => $record->pivot?->enrolled_at,
                    ])
                    ->form([
                        Select::make('status')
                            ->options(StudentCourseStatus::class)
                            ->required(),

                        DatePicker::make('enrolled_at')
                            ->required(),
                    ])
                    ->action(function (Model $record, array $data) {
                        $this->getOwnerRecord()
                            ->students()
                            ->updateExistingPivot($record->getKey(), [
                                'status' => $data['status'],
                                'enrolled_at' => $data['enrolled_at'],
                            ]);
                    }),

                DetachAction::make()
                    ->label('Unenroll'),
            ])
            ->toolbarActions([
                DetachBulkAction::make()
                    ->label('Unenroll selected'),
            ]);
    }
}
