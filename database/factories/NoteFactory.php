<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{

    protected $model = Note::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = User::query()->inRandomOrder()->value('id');
        $courseId = null;

        // 45% of notes are tied to a course (optional relationship)
        if ($this->faker->boolean(45)) {
            $courseId = Course::query()->inRandomOrder()->value('id');
        }

        return [
            'user_id' => $userId,
            'course_id' => $courseId,
            'title' => $this->faker->boolean(80) ? $this->faker->sentence(4) : null,
            'content' => $this->faker->paragraphs(3, true), // long text
        ];
    }
}
