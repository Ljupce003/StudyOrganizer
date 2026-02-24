<?php

namespace Database\Seeders;

use App\Enums\StudentCourseStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseRelationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $students = User::where('role', UserRole::STUDENT)->get();
        $professors = User::where('role', UserRole::PROFESSOR)->get();
        $courses = Course::all();

        if ($students->isEmpty() || $professors->isEmpty() || $courses->isEmpty()) {
            return;
        }

        foreach ($courses as $course) {

            // Attach 1–2 professors per course
            $course->professors()->syncWithPivotValues(
                $professors->random(rand(1, min(2, $professors->count())))
                    ->pluck('id'),[
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Attach 2–5 students per course
            $course->students()->syncWithPivotValues(
                $students->random(rand(2, min(5, $students->count())))
                    ->pluck('id'),
                [
                    'status' => StudentCourseStatus::ENROLLED->value,
                    'enrolled_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
