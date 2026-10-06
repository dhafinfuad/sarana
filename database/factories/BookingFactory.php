<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     * Default menghasilkan booking berstatus 'pending'.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tanggalMulai   = Carbon::instance($this->faker->dateTimeBetween('-3 months', '+1 month'));
        $tanggalSelesai = $tanggalMulai->copy()->addDays($this->faker->numberBetween(1, 7));

        return [
            'user_id'        => User::factory(),
            'vehicle_id'     => Vehicle::factory(),
            'provinsi'       => $this->faker->state(),
            'kota'           => $this->faker->city(),
            'keperluan'      => $this->faker->sentence(8),
            'tanggal_mulai'  => $tanggalMulai->toDateString(),
            'tanggal_selesai'=> $tanggalSelesai->toDateString(),
            'status'         => BookingStatus::Pending,
            'catatan_admin'  => null,
            'processed_by'   => null,
            'processed_at'   => null,
        ];
    }

    // -----------------------------------------------------------------------
    // States — Status Booking
    // -----------------------------------------------------------------------

    /**
     * State: booking berstatus 'pending' (menunggu persetujuan admin).
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status'       => BookingStatus::Pending,
            'processed_by' => null,
            'processed_at' => null,
            'catatan_admin'=> null,
        ]);
    }

    /**
     * State: booking berstatus 'disetujui'.
     * Otomatis mengisi processed_by dengan user Administrator.
     */
    public function disetujui(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status'       => BookingStatus::Disetujui,
            'processed_by' => User::factory()->administrator(),
            'processed_at' => Carbon::now()->subHours($this->faker->numberBetween(1, 48)),
        ]);
    }

    /**
     * State: booking berstatus 'ditolak'.
     */
    public function ditolak(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status'        => BookingStatus::Ditolak,
            'processed_by'  => User::factory()->administrator(),
            'processed_at'  => Carbon::now()->subHours($this->faker->numberBetween(1, 48)),
            'catatan_admin' => $this->faker->sentence(5),
        ]);
    }

    /**
     * State: booking berstatus 'selesai' (kendaraan sudah dikembalikan).
     */
    public function selesai(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status'       => BookingStatus::Selesai,
            'processed_by' => User::factory()->administrator(),
            'processed_at' => Carbon::now()->subDays($this->faker->numberBetween(1, 7)),
        ]);
    }

    /**
     * State: booking berstatus 'dibatalkan' (dibatalkan darurat oleh admin).
     */
    public function dibatalkan(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status'        => BookingStatus::Dibatalkan,
            'processed_by'  => User::factory()->administrator(),
            'processed_at'  => Carbon::now()->subHours($this->faker->numberBetween(1, 24)),
            'catatan_admin' => $this->faker->sentence(4),
        ]);
    }

    // -----------------------------------------------------------------------
    // States — Kontekstual
    // -----------------------------------------------------------------------

    /**
     * State: gunakan User dan Vehicle yang sudah ada (tidak membuat baru).
     * Berguna saat seeding agar tidak membuat terlalu banyak record.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * State: gunakan Vehicle yang sudah ada.
     */
    public function forVehicle(Vehicle $vehicle): static
    {
        return $this->state(fn (array $attributes): array => [
            'vehicle_id' => $vehicle->id,
        ]);
    }

    /**
     * State: booking untuk perjalanan luar kota.
     */
    public function luarKota(): static
    {
        return $this->state(fn (array $attributes): array => [
            'provinsi' => 'Luar Provinsi',
        ]);
    }

    /**
     * State: booking untuk perjalanan dalam kota.
     */
    public function dalamKota(): static
    {
        return $this->state(fn (array $attributes): array => [
            'provinsi' => 'Satu Provinsi',
        ]);
    }
}
