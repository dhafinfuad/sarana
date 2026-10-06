<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardActionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '10.12.13.225',
            'database.connections.mysql.database' => 'db_aplikasi',
        ]);
        \Illuminate\Support\Facades\DB::purge();
    }

    public function test_admin_reject_booking_flashes_success_notification(): void
    {
        $admin = User::find(147); // Dharma Setiawan (Subbagian Umum)
        $this->assertNotNull($admin);
        $this->assertTrue($admin->canManageBookings());

        $user = User::first();
        $vehicle = Vehicle::first();
        
        $booking = Booking::create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'provinsi' => 'Jawa Timur',
            'kota' => 'Surabaya',
            'keperluan' => 'Test Penolakan Peminjaman',
            'tanggal_mulai' => now()->addDays(20)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(21)->format('Y-m-d'),
            'status' => 'pending',
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Dashboard::class)
            ->call('reject', $booking->id)
            ->assertDispatched('booking-updated')
            ->assertSee('berhasil ditolak');

        $booking->refresh();
        $this->assertEquals('ditolak', $booking->status->value);

        // Clean up test booking
        $booking->forceDelete();
    }

    public function test_admin_delete_booking_flashes_success_notification(): void
    {
        $admin = User::find(147);
        $this->assertNotNull($admin);
        $this->assertTrue($admin->canManageBookings());

        $user = User::first();
        $vehicle = Vehicle::first();
        
        $booking = Booking::create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'provinsi' => 'Jawa Timur',
            'kota' => 'Surabaya',
            'keperluan' => 'Test Hapus Peminjaman',
            'tanggal_mulai' => now()->addDays(25)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(26)->format('Y-m-d'),
            'status' => 'pending',
        ]);

        $bookingId = $booking->id;

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Dashboard::class)
            ->call('deleteBooking', $bookingId)
            ->assertDispatched('booking-updated')
            ->assertSee('berhasil dihapus');

        $this->assertNull(Booking::find($bookingId));
    }

    public function test_admin_approve_booking_flashes_success_notification(): void
    {
        $admin = User::find(147);
        $this->assertNotNull($admin);

        $user = User::first();
        $vehicle = Vehicle::first();
        
        $booking = Booking::create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'provinsi' => 'Jawa Timur',
            'kota' => 'Surabaya',
            'keperluan' => 'Test Setujui Peminjaman',
            'tanggal_mulai' => now()->addDays(30)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(31)->format('Y-m-d'),
            'status' => 'pending',
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Dashboard::class)
            ->call('approve', $booking->id)
            ->assertDispatched('booking-updated')
            ->assertSee('berhasil disetujui');

        $booking->refresh();
        $this->assertEquals('disetujui', $booking->status->value);

        $booking->forceDelete();
    }
}
