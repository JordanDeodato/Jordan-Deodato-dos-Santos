<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\VacancyType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vacancy>
 */
class VacancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraph(),
            'vacancy_type_id' => VacancyType::inRandomOrder()->first()?->id ?? 1,
            'recruiter_uuid' => User::where('user_type_id', 1)->inRandomOrder()->first()?->uuid ?? Str::uuid(),
            'opened' => $this->faker->boolean(80),
        ];
    }
}
