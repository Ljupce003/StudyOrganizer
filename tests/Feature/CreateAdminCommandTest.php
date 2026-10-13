<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_a_hashed_admin_that_can_access_the_production_panel(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Name', 'Admin User')
            ->expectsQuestion('Email', 'admin@example.com')
            ->expectsQuestion('Password', 'StrongAdminPassword123!')
            ->expectsQuestion('Confirm password', 'StrongAdminPassword123!')
            ->expectsOutput('Administrator created: admin@example.com')
            ->assertSuccessful();

        $user = User::where('email', 'admin@example.com')->firstOrFail();
        $this->assertSame(UserRole::ADMIN, $user->role);
        $this->assertTrue(Hash::check('StrongAdminPassword123!', $user->password));

        config(['app.env' => 'production']);
        $this->withoutVite();
        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_name_and_email_options_are_trimmed_and_email_is_lowercased(): void
    {
        $this->artisan('admin:create', ['--name' => ' CLI Admin ', '--email' => ' ADMIN@EXAMPLE.COM '])
            ->expectsQuestion('Password', 'StrongAdminPassword123!')
            ->expectsQuestion('Confirm password', 'StrongAdminPassword123!')
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'name' => 'CLI Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_duplicate_email_does_not_promote_or_overwrite_an_existing_user(): void
    {
        $existing = User::factory()->student()->create(['email' => 'Student@Example.com']);
        $originalPassword = $existing->password;

        $this->artisan('admin:create', ['--name' => 'Another User', '--email' => 'student@example.com'])
            ->expectsOutput('An account with this email already exists. No account was changed.')
            ->assertFailed();

        $this->assertDatabaseCount('users', 1);
        $this->assertSame(UserRole::STUDENT, $existing->fresh()->role);
        $this->assertSame($originalPassword, $existing->fresh()->password);
    }

    public function test_invalid_email_is_rejected_without_creating_a_user(): void
    {
        $this->artisan('admin:create', ['--name' => 'Admin', '--email' => 'not-an-email'])
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_empty_name_is_rejected_without_creating_a_user(): void
    {
        $this->artisan('admin:create', ['--name' => ' ', '--email' => 'admin@example.com'])
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_short_password_is_rejected(): void
    {
        $this->artisan('admin:create', ['--name' => 'Admin', '--email' => 'admin@example.com'])
            ->expectsQuestion('Password', 'short')
            ->expectsQuestion('Confirm password', 'short')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_password_confirmation_must_match(): void
    {
        $this->artisan('admin:create', ['--name' => 'Admin', '--email' => 'admin@example.com'])
            ->expectsQuestion('Password', 'StrongAdminPassword123!')
            ->expectsQuestion('Confirm password', 'DifferentPassword123!')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_non_interactive_invocation_fails_without_creating_a_user(): void
    {
        $this->artisan('admin:create', ['--name' => 'Admin', '--email' => 'admin@example.com', '--no-interaction' => true])
            ->expectsOutput('Run admin:create interactively to enter the password securely.')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }
}
