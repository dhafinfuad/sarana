<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);

        // Buat beberapa user tambahan
        User::factory()->count(10)->create();

        // Buat kendaraan
        Vehicle::factory()->count(15)->create();

        // Buat booking acak
        Booking::factory()->count(50)->create();

        $this->command->info('✅ DatabaseSeeder: Dummy Vehicles and Bookings berhasil dibuat.');
    }
}
