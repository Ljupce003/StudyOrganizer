<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        // Most notes should belong to students
        $students = User::where('role', UserRole::STUDENT)->get();
        $professors = User::where('role', UserRole::PROFESSOR)->get();

        if ($students->isEmpty()) {
            return;
        }

        // 5–10 notes per student
        foreach ($students as $student) {
            Note::factory()
                ->count(rand(5, 10))
                ->create(['user_id' => $student->id]);
        }

        // Optional: a few notes for professors too (2–5 each)
        foreach ($professors as $professor) {
            Note::factory()
                ->count(rand(2, 5))
                ->create(['user_id' => $professor->id]);
        }
    }
}
