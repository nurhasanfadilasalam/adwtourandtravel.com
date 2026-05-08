<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\JadwalKeberangkatan;
use App\Models\PaketUmroh;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::inRandomOrder()->first()->id ?? Customer::factory(),
            'paket_umroh_id' => PaketUmroh::inRandomOrder()->first()->id ?? PaketUmroh::factory(),
            'jadwal_keberangkatan_id' => JadwalKeberangkatan::inRandomOrder()->first()->id ?? JadwalKeberangkatan::factory(),
            'booking_code' => 'BK-' . Str::upper(Str::random(8)),
            'status' => $this->faker->randomElement(['pending', 'paid', 'canceled']),
            'total_price' => $this->faker->randomElement([30000000, 35000000, 40000000]),
            'metode_pembayaran' => $this->faker->randomElement(['cash', 'transfer', 'cicilan']),
            'created_by' => User::inRandomOrder()->first()->id ?? User::factory(),

        ];
    }
}
