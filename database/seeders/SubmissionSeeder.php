<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $assignments = Assignment::query()->get();

        if ($assignments->isEmpty()) {
            $this->command?->warn('No assignments found. Skipping SubmissionSeeder.');
            return;
        }

        // Cache users by role (fallbacks)
//        $adminIds = User::query()->where('role', UserRole::ADMIN)->pluck('id');
        $anyProfessorIds = User::query()->where('role', UserRole::PROFESSOR)->pluck('id');

        foreach ($assignments as $assignment) {
            // Students enrolled in the assignment's course
            $studentIds = DB::table('course_user')
                ->where('course_id', $assignment->course_id)
                ->pluck('user_id');

            if ($studentIds->isEmpty()) {
                // No enrolled students → nothing to seed realistically
                continue;
            }

            // Professors teaching this course
            $professorIds = DB::table('course_professor')
                ->where('course_id', $assignment->course_id)
                ->pluck('user_id');

            // Choose how many students will submit (40%–90% of enrolled)
            $countStudents = $studentIds->count();
            $submitterCount = max(1, (int) round($countStudents * rand(40, 90) / 100));

            $submitters = $studentIds->shuffle()->take($submitterCount);

            foreach ($submitters as $studentId) {
                // number_attempts: null => unlimited; else cap attempts 1..number_attempts
                $maxAttempts = $assignment->number_attempts; // nullable
                $attemptsToCreate = $maxAttempts
                    ? rand(1, min(3, (int) $maxAttempts)) // keep small for seed realism
                    : rand(1, 2);

                // Prevent duplicates beyond attempts (unique by assignment_id + student_id + attempt index is not in schema)
                // Since schema doesn't store attempt_number, we just ensure at most one submission per student per assignment
                // OR: if you *intend* multiple attempts, you'll need an attempt_number column later.
                $attemptsToCreate = 1;

                for ($i = 0; $i < $attemptsToCreate; $i++) {
                    $submission = Submission::factory()->make();

                    $submission->grade = $submission->graded_at!=null ? fake()->numberBetween(0, $assignment->max_points) : null;

                    $submission->assignment_id = $assignment->id;
                    $submission->student_id = $studentId;

                    // If graded, choose grader
                    if (!is_null($submission->grade)) {
                        $graderPool = $professorIds->isNotEmpty()
                            ? $professorIds
                            : $anyProfessorIds;

                        $submission->graded_by = $graderPool->isNotEmpty()
                            ? $graderPool->random()
                            : null;

                        // graded_at should exist if grade exists
                        $submission->graded_at = $submission->graded_at ?? now();
                    } else {
                        $submission->graded_by = null;
                        $submission->graded_at = null;
                        $submission->feedback = null;
                    }

                    // submitted_at consistency: if no submitted_at, then also no grading
                    if (is_null($submission->submitted_at)) {
                        $submission->grade = null;
                        $submission->graded_by = null;
                        $submission->graded_at = null;
                        $submission->feedback = null;
                    }

                    $submission->save();
                }
            }
        }
    }
}
