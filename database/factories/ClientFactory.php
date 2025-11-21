<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'naziv_firme' => fake()->company(),
            'adresa' => fake()->address(),
            'postanski_broj_mjesto_drzava' => fake()->city().' '.fake()->postcode().', '.fake()->country(),
            'pdv_broj' => fake()->numerify('PDV-###########'),
            'email' => fake()->safeEmail(),
            'kontakt_telefon' => fake()->phoneNumber(),
        ];
    }

    /**
     * Indicate that the client has no email address.
     */
    public function withoutEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => '',
        ]);
    }
}
