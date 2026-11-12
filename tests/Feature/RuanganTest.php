<?php

namespace Tests\Feature;

use App\Livewire\Ruangan\Index;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class RuanganTest extends TestCase
{
    use RefreshDatabase;

    private static int $seq = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // Tabel plh_plt tidak punya migrasi di repo (hanya ada di DB produksi),
        // padahal dibaca oleh User::getActivePlhPlt(); buat versi minimal untuk tes.
        if (! Schema::hasTable('plh_plt')) {
            Schema::create('plh_plt', function (Blueprint $t) {
                $t->id();
                $t->string('posisi')->nullable();
                $t->string('jenis')->nullable();
                $t->unsignedBigInteger('pegawai_id')->nullable();
                $t->unsignedBigInteger('pejabat_definitif_id')->nullable();
                $t->string('no_sp')->nullable();
                $t->date('tanggal_sp')->nullable();
                $t->date('tanggal_mulai')->nullable();
                $t->date('tanggal_selesai')->nullable();
                $t->boolean('status_aktif')->default(true);
                $t->text('keterangan')->nullable();
                $t->timestamps();
            });
        }
    }

    // UserFactory bawaan repo sudah usang (merujuk UserRole), jadi user dibuat langsung.
    private function makeUser(array $attrs): User
    {
        return User::forceCreate(array_merge([
            'name' => 'Tester ' . (++self::$seq),
            'nip_pendek' => str_pad((string) (900000000 + self::$seq), 9, '0'),
            'password' => 'password',
        ], $attrs));
    }

    private function pegawai(): User
    {
        return $this->makeUser(['jabatan' => 'Pelaksana', 'seksi' => 'Seksi Pelayanan']);
    }

    private function admin(): User
    {
        return $this->makeUser(['jabatan' => 'Administrator']);
    }

    private function room(array $attrs = []): Room
    {
        return Room::create(array_merge(['nama' => 'Ruang Rapat A', 'kapasitas' => 10], $attrs));
    }

    private function booking(Room $room, User $user, string $mulai, string $selesai, string $status = 'pending'): RoomBooking
    {
        return RoomBooking::create([
            'room_id' => $room->id, 'user_id' => $user->id, 'keperluan' => 'Rapat koordinasi',
            'jumlah_peserta' => 5, 'tanggal_mulai' => $mulai, 'tanggal_selesai' => $selesai, 'status' => $status,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/ruangan/peminjaman')->assertRedirect('/login');
    }

    public function test_pegawai_cannot_open_persetujuan_page(): void
    {
        $this->actingAs($this->pegawai())->get('/ruangan/persetujuan')->assertForbidden();
    }

    public function test_pegawai_can_submit_booking(): void
    {
        $room = $this->room();
        $user = $this->pegawai();

        Livewire::actingAs($user)->test(Index::class)
            ->set('roomId', (string) $room->id)
            ->set('keperluan', 'Rapat koordinasi')
            ->set('jumlahPeserta', '5')
            ->set('tanggalMulai', now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i'))
            ->set('tanggalSelesai', now()->addDay()->setTime(11, 0)->format('Y-m-d\TH:i'))
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('room_bookings', ['room_id' => $room->id, 'user_id' => $user->id, 'status' => 'pending']);
    }

    public function test_overlapping_booking_is_rejected(): void
    {
        $room = $this->room();
        $this->booking($room, $this->pegawai(), now()->addDay()->setTime(9, 0), now()->addDay()->setTime(11, 0));

        Livewire::actingAs($this->pegawai())->test(Index::class)
            ->set('roomId', (string) $room->id)
            ->set('keperluan', 'Rapat lain')
            ->set('jumlahPeserta', '3')
            ->set('tanggalMulai', now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'))
            ->set('tanggalSelesai', now()->addDay()->setTime(12, 0)->format('Y-m-d\TH:i'))
            ->call('submit')
            ->assertHasErrors(['tanggalMulai']);

        $this->assertDatabaseCount('room_bookings', 1);
    }

    public function test_participants_over_capacity_is_rejected(): void
    {
        $room = $this->room(['kapasitas' => 4]);

        Livewire::actingAs($this->pegawai())->test(Index::class)
            ->set('roomId', (string) $room->id)
            ->set('keperluan', 'Rapat besar')
            ->set('jumlahPeserta', '9')
            ->set('tanggalMulai', now()->addDay()->format('Y-m-d\TH:i'))
            ->set('tanggalSelesai', now()->addDay()->addHour()->format('Y-m-d\TH:i'))
            ->call('submit')
            ->assertHasErrors(['jumlahPeserta']);
    }

    public function test_admin_can_approve_and_conflicting_approval_is_blocked(): void
    {
        $room = $this->room();
        $user = $this->pegawai();
        $a = $this->booking($room, $user, now()->addDay()->setTime(9, 0), now()->addDay()->setTime(11, 0));
        $b = $this->booking($room, $user, now()->addDay()->setTime(10, 0), now()->addDay()->setTime(12, 0));

        $admin = $this->admin();
        Livewire::actingAs($admin)
            ->test(Index::class, ['mode' => 'reviewer'])
            ->call('approve', $a->id)
            ->call('approve', $b->id);

        $this->assertSame('disetujui', $a->fresh()->status);
        $this->assertSame('pending', $b->fresh()->status);
    }

    public function test_pegawai_can_cancel_only_own_pending_booking(): void
    {
        $room = $this->room();
        $owner = $this->pegawai();
        $other = $this->pegawai();
        $booking = $this->booking($room, $owner, now()->addDay(), now()->addDay()->addHour());

        Livewire::actingAs($other)->test(Index::class)->call('cancel', $booking->id);
        $this->assertSame('pending', $booking->fresh()->status);

        Livewire::actingAs($owner)->test(Index::class)->call('cancel', $booking->id);
        $this->assertSame('dibatalkan', $booking->fresh()->status);
    }
}
