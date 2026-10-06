<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UserRole;
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
     * Password default yang di-reuse agar tidak di-hash berulang kali.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     * Default menghasilkan user dengan role 'pegawai'.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // NIP pendek: format 10 digit numerik unik
        $nipPendek = (string) $this->faker->unique()->numerify('##########');

        return [
            'nip_pendek'   => $nipPendek,
            'name' => fake()->name(),
            'seksi'        => $this->faker->randomElement([
                'Seksi Pengembangan SDM',
                'Seksi Teknologi Informasi',
                'Seksi Keuangan',
                'Seksi Umum',
                'Seksi Pelayanan',
            ]),
            'password'     => static::$password ??= Hash::make('password'),
            'role'         => UserRole::Pegawai,
            'remember_token' => Str::random(10),
        ];
    }

    // -----------------------------------------------------------------------
    // States
    // -----------------------------------------------------------------------

    /**
     * State: jadikan user ini Administrator.
     */
    public function administrator(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => UserRole::Administrator,
        ]);
    }

    /**
     * State: jadikan user ini Kepala Kantor.
     */
    public function kepalaKantor(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => UserRole::KepalaKantor,
        ]);
    }

    /**
     * State: jadikan user ini Pegawai (default, tapi tersedia secara eksplisit).
     */
    public function pegawai(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => UserRole::Pegawai,
        ]);
    }
}
