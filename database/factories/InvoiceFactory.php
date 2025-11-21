<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'klijent_id' => Client::factory(),
            'broj_fakture' => '#'.fake()->numberBetween(1, 999).'/'.now()->year,
            'datum_izdavanja' => now(),
            'opis_posla' => fake()->sentence(),
            'kolicina' => fake()->numberBetween(1, 100),
            'cijena' => fake()->randomFloat(2, 100, 10000),
            'valuta' => fake()->randomElement(['BAM', 'EUR']),
            'placeno' => false,
            'datum_placanja' => null,
            'uplaceni_iznos_eur' => null,
        ];
    }
}
