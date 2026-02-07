<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->count(2)->student()->create();
        User::factory()->student()->create([
            'email' => "s@ex.com",
            'name' => "Student User"
        ]);

        User::factory()->count(2)->professor()->create();
        User::factory()->professor()->create([
                'email' => "p@ex.com",
                'name' => "Professor User"
            ]);
        User::factory()->admin()->create([
            'email' => 'a@ex.com', // so you can log in easily
            'name' => 'Admin User',
        ]);

        $this->call([
            CourseSeeder::class,
            CourseRelationsSeeder::class,
            AssignmentSeeder::class,
            NoteSeeder::class,
            SubmissionSeeder::class,
        ]);
    }
}
