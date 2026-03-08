<?php

namespace Database\Factories;

use App\Enums\SemesterType;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

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
        $year = $this->faker->numberBetween(now()->year - 3, now()->year);
        $semester = $this->faker->randomElement(Collection::make(SemesterType::cases())->pluck("value")->toArray());

        $codePrefix = $this->faker->randomElement(['CS', 'SE', 'IT', 'DS']);
        $codeNumber = $this->faker->numberBetween(1, 9) . substr($year."",2,2);
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
