<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bandara>
 */
class BandaraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_bandara' => strtoupper($this->faker->lexify('???')),
            'nama_bandara' => $this->faker->company() . ' International Airport',
            'kota_bandara' => $this->faker->city(),
        ];
    }
}
