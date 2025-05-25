<?php

namespace Database\Factories;

use App\Models\Education;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $education = Education::inRandomOrder()->first() ?? Education::factory()->create();

        return [
            'user_uuid' => User::where('user_type_id', 2)->inRandomOrder()->first()?->uuid ?? Str::uuid(),
            'resume' => $this->faker->url(),
            'education_id' => $education->id,
            'experience' => $this->faker->paragraphs(3, true),
            'skills' => $this->faker->words(5, true),
            'linkedin_profile' => $this->faker->url(),
        ];
    }
}
