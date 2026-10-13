<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Filament permits users without its contract locally. Exercise the
        // production check that previously rejected even administrator users.
        config(['app.env' => 'production']);
        $this->withoutVite();
    }

    public function test_admin_can_open_the_panel_in_production(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_student_cannot_open_the_panel_in_production(): void
    {
        $user = User::factory()->student()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_professor_cannot_open_the_panel_in_production(): void
    {
        $user = User::factory()->professor()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_user_promoted_to_admin_can_open_the_panel(): void
    {
        $user = User::factory()->student()->create();
        $user->role = UserRole::ADMIN;
        $user->save();

        $this->actingAs($user->fresh())->get('/admin')->assertOk();
    }
}
