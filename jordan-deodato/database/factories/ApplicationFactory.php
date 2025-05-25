<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'candidate_uuid' => User::factory()->create(['user_type_id' => 2])->uuid,
            'vacancy_uuid' => Vacancy::factory()->create()->uuid,
        ];
    }
}
