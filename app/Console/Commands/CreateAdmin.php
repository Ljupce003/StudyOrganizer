<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Closure;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create
        {--name= : Administrator name}
        {--email= : Administrator email address}';

    protected $description = 'Create a new StudyOrganizer administrator';

    public function handle(): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('Run admin:create interactively to enter the password securely.');

            return self::FAILURE;
        }

        $name = trim((string) ($this->option('name') ?? $this->ask('Name')));
        $email = Str::lower(trim((string) ($this->option('email') ?? $this->ask('Email'))));

        $identity = Validator::make(compact('name', 'email'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (User::query()->whereRaw('LOWER(email) = ?', [$value])->exists()) {
                        $fail('An account with this email already exists. No account was changed.');
                    }
                },
            ],
        ]);

        if ($identity->fails()) {
            foreach ($identity->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $password = $this->secret('Password', false);
        $confirmation = $this->secret('Confirm password', false);
        $credentials = Validator::make([
            'password' => $password,
            'password_confirmation' => $confirmation,
        ], [
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        if ($credentials->fails()) {
            foreach ($credentials->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        try {
            User::create([
                'name' => $name,
                'email' => $email,
                // The User model's hashed cast handles password hashing.
                'password' => $password,
                'role' => UserRole::ADMIN,
            ]);
        } catch (UniqueConstraintViolationException) {
            $this->error('An account with this email already exists. No account was changed.');

            return self::FAILURE;
        }

        $this->info("Administrator created: {$email}");
        $this->line('Log in through /login to access /admin.');

        return self::SUCCESS;
    }
}
