<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker->addProvider(new \Faker\Provider\pt_BR\Person($this->faker));
        
        return [
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'cpf' => $this->faker->unique()->cpf(false),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'user_type_id' => 1,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * State for the email mnot verified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * State for the recruiter user
     */
    public function recruiter(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type_id' => 2,
        ]);
    }

    /**
     * State for the candidate user
     */
    public function candidate(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type_id' => 1,
        ]);
    }
}
