<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\table;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('changePassword')
                ->label('Change Password')
                ->schema([
                    TextInput::make('new_password')
                        ->label('New Password')
                        ->password()
                        ->required()
                        ->minLength(8),
                    TextInput::make('new_password_confirmation')
                        ->label('Confirm Password')
                        ->password()
                        ->required()
                        ->same('new_password'),
                    Toggle::make('logout_other_devices')
                        ->label('Log out of other devices')
                        ->default(true),
                ])
                ->action(function (array $data, User $record) {
                    $record->password = $data['new_password'];
                    $record->save();

                    if (!empty($data['logout_other_devices'])) {
                        // You must pass the current password (before change) to logoutOtherDevices
//                        Auth::logoutOtherDevices($data['new_password']); // but see note below
                        DB::table("sessions")
                            ->where('user_id',$record->id)
                            ->delete();
                    }

                    Notification::make()
                        ->title('Password updated')
                        ->success()
                        ->send();
                }),
        ];
    }
}
