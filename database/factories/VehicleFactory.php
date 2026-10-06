<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     * Default menghasilkan kendaraan dengan status 'tersedia'.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $merek = $this->faker->randomElement([
            'Toyota',
            'Honda',
            'Mitsubishi',
            'Isuzu',
            'Daihatsu',
        ]);

        $tipe = $this->faker->randomElement([
            'Kijang Innova',
            'HiAce',
            'Pajero Sport',
            'Avanza',
            'Fortuner',
            'D-Max',
            'Rush',
            'Terios',
        ]);

        // Plat nomor format: [Huruf][Angka][Huruf]  contoh: B 1234 ABC
        $platNomor = strtoupper($this->faker->lexify('?'))
            . ' '
            . $this->faker->numerify('####')
            . ' '
            . strtoupper($this->faker->lexify('???'));

        return [
            'plat_nomor'     => $platNomor,
            'nama_kendaraan' => "{$merek} {$tipe}",
            'status'         => VehicleStatus::Tersedia,
        ];
    }

    // -----------------------------------------------------------------------
    // States
    // -----------------------------------------------------------------------

    /**
     * State: kendaraan berstatus 'tersedia'.
     */
    public function tersedia(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => VehicleStatus::Tersedia,
        ]);
    }

    /**
     * State: kendaraan berstatus 'tidak_tersedia' (maintenance / rusak).
     */
    public function tidakTersedia(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => VehicleStatus::TidakTersedia,
        ]);
    }
}
