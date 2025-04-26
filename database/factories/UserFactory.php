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
            'role' => '',
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
                'last_name' => 'Úr',
                'email' => 'admin@jedlik.eu',  // Beállíthatod az email-t "admin@example.com"-ra
                'username' => 'admin',           // Felhasználónév "admin"
                'password' => bcrypt('admin'),  // Jelszó "admin"
                'role' => 'admin',
            ];
        });
    }

        /**
     * Teszt felhasználó létrehozása.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function teszt(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'first_name' => 'Teszt',
                'last_name' => 'Elek',
                'birth_place' => 'Győr',
                'email' => 'teszt@jedlik.eu',
                'username' => 'teszt',
                'password' => bcrypt('teszt'),
            ];
        });
    }
}
