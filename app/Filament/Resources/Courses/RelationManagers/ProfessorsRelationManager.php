<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
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
                AttachAction::make()
                    ->label('Assign professor')
                    ->recordSelectSearchColumns(['name', 'email'])
                    ->recordTitle(fn($record) => $record->name . " - " . $record->email)
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn($query) =>
                    $query->whereDoesntHave('teachingCourses', fn($q) => $q
                        ->where('courses.id', $this->getOwnerRecord()->id)
                    ))
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
