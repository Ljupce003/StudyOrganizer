<?php

namespace Database\Factories;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
{

    protected $model = Submission::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $submittedAt = $this->faker->optional(0.85)->dateTimeBetween('-30 days', 'now'); // 85% submitted

        $isGraded = $submittedAt && $this->faker->boolean(65); // only graded if submitted, 65% chance

//        $grade = $isGraded ? $this->faker->numberBetween(0, 100) : null;

        $gradedAt = $isGraded
            ? $this->faker->dateTimeBetween($submittedAt, 'now')
            : null;

        return [
            'assignment_id' => null,  // set in seeder
            'student_id'    => null,  // set in seeder

            'content' => $this->faker->optional(0.8)->paragraphs($this->faker->numberBetween(1, 3), true),
            'attachment_path' => null, // later

            'submitted_at' => $submittedAt,

            'grade' => null, // later in course
            'feedback' => $isGraded
                ? $this->faker->optional(0.7)->paragraphs($this->faker->numberBetween(1, 2), true)
                : null,

            'graded_by' => null, // set in seeder if graded
            'graded_at' => $gradedAt,
        ];
    }

    public function submitted(): static
    {
        return $this->state(function () {
            $submittedAt = $this->faker->dateTimeBetween('-30 days', 'now');

            return [
                'submitted_at' => $submittedAt,
            ];
        });
    }

    public function graded(): static
    {
        return $this->state(function () {
            $submittedAt = $this->faker->dateTimeBetween('-30 days', '-1 days');
            $gradedAt = $this->faker->dateTimeBetween($submittedAt, 'now');

            return [
                'submitted_at' => $submittedAt,
                'grade' => $this->faker->numberBetween(0, 100),
                'feedback' => $this->faker->optional(0.7)->sentence(),
                'graded_at' => $gradedAt,
            ];
        });
    }
}
