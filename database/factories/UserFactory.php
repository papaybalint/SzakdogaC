<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'birth_place' => $this->faker->city(),
            'birth_date' => $this->faker->date(),
            'phone' => '+36 20 ' . $this->faker->numerify('#######'),
            'email' => $this->faker->unique()->safeEmail(),
            'username' => $this->faker->unique()->userName(),
            'password' => bcrypt('password'),
        ];
    }

    /**
     * Admin felhasználó létrehozása.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function admin(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'email' => 'admin@admin.admin',  // Beállíthatod az email-t "admin@example.com"-ra
                'username' => 'admin',           // Felhasználónév "admin"
                'password' => bcrypt('admin'),  // Jelszó "admin"
                'role' => 'admin',
            ];
        });
    }
}
