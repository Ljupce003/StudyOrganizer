<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\QueryException;

class ProfessorsRelationManager extends RelationManager
{
    protected static string $relationship = 'professors';

    protected static ?string $title = 'Professors';

    protected static ?string $label = 'Professor';
    protected static ?string $pluralLabel = 'Professors';

    protected static ?string $relatedResource = UserResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
            ])
            ->headerActions([
                Action::make("assignProfessor")
                    ->label('Assign professor')
                    ->schema([
                        Select::make("user_id")
                            ->label("Professor")
                            ->searchable()
                            ->options(fn() => User::query()
                                ->where('role', UserRole::PROFESSOR)
                                ->whereDoesntHave('teachingCourses', fn($query) =>
                                    $query->where("courses.id", $this->getOwnerRecord()->id))
                            ->orderBy('name')
                                ->get()
                                ->mapWithKeys(fn($user) => [$user->id => "$user->name - $user->email"])
                            )
                        ->required(),
                    ])
                ->action(function (array $data){
                    try {
                        $this->getOwnerRecord()
                            ->professors()
                            ->attach($data['user_id'],['created_at' => \Symfony\Component\Clock\now()]);
                    } catch (QueryException $e){
                        Notification::make()
                            ->title("Assignment Error")
                            ->body("$e")
                            ->danger()
                            ->send();
                    }
                })
//                AttachAction::make()
//                    ->label('Assign professor')
//                    ->recordSelectSearchColumns(['name', 'email'])
//                    ->recordTitle(fn($record) => $record->name . " - " . $record->email)
//                    ->preloadRecordSelect()
//                    ->recordSelectOptionsQuery(fn($query) =>
//                    $query->whereDoesntHave('teachingCourses', fn($q) => $q
//                        ->where('courses.id', $this->getOwnerRecord()->id)
//                    ))
//                    ->action(function (array $data) {
//                        try {
//                            // default attach behavior:
//                            $this->getOwnerRecord()
//                                ->professors()
//                                ->attach($data['recordId']);
//                        } catch (QueryException $e) {
//                            // 23000 is the common SQLSTATE for integrity constraint violation
//                            if ($e->getCode() === '23000') {
//                                Notification::make()
//                                    ->title('Already assigned')
//                                    ->body('That professor is already assigned to this course.')
//                                    ->danger()
//                                    ->send();
//
//                                return;
//                            }
//
//                            throw $e; // unknown DB error -> don’t hide it
//                        }
//                    }),
            ])
            ->recordActions([
                DetachAction::make()->label('Remove'),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}
