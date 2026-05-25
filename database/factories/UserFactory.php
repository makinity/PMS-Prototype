<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employeeNumber = $this->faker->unique()->numberBetween(1, 99999);

        return [
            'employee_id' => 'EMP-2026-' . str_pad((string) $employeeNumber, 5, '0', STR_PAD_LEFT),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // factory default; demo users override to null in DemoUsersSeeder
            'remember_token' => Str::random(10),

            'role' => 'employee',
            'is_active' => false,
            'activated_at' => null,
        ];
    }
}
