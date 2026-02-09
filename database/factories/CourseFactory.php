<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Course::class;
    public function definition(): array
    {
        $year = now()->year;
        $semester = $this->faker->randomElement(['Fall', 'Spring']);

        $codePrefix = $this->faker->randomElement(['CS', 'SE', 'IT', 'DS']);
        $codeNumber = $this->faker->numberBetween(100, 499);
        $code = $codePrefix . $codeNumber;

        return [
            'code' => $code,
            'name' => $this->faker->sentence(3),
            'short_name' => $code,
            'semester' => $semester,
            'year' => $year,
            'is_active' => true,

            // assumes at least one user exists (admin/professor)
            'created_by' => User::query()->where("users.role",UserRole::ADMIN)->inRandomOrder()->value('id'),
        ];
    }
}
