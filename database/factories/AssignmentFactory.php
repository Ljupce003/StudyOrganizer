<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Type\Integer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Assignment::class;

    public function definition(): array
    {
        $dueAt = $this->faker->dateTimeBetween('+3 days', '+3 weeks');

        return [
            'course_id' => Course::query()->inRandomOrder()->value('id'),

            // creator should realistically be a professor or admin
            'created_by' => User::query()
                ->whereIn('role', [ UserRole::ADMIN->value])
                ->inRandomOrder()
                ->value('id'),

            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),

            'due_at' => $dueAt,
            'allow_late' => $this->faker->boolean(20), // 20% allow late
            'max_points' => $this->faker->randomElement([10, 20, 50, 100]),
            'number_attempts' => $this->faker->randomElement([1,2,10,null]),

            'is_published' => $this->faker->boolean(80), // most assignments visible
        ];
    }
}
