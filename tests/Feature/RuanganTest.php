<?php

namespace Tests\Feature;

use App\Livewire\Ruangan\Index;
use App\Livewire\Ruangan\Kelola;
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
    // ---- Kelola Ruangan ----

    public function test_pegawai_cannot_open_kelola_page(): void
    {
        $this->actingAs($this->pegawai())->get('/ruangan/kelola')->assertForbidden();
    }

    public function test_admin_can_open_kelola_page(): void
    {
        $this->actingAs($this->admin())->get('/ruangan/kelola')->assertOk()->assertSee('Tambah Ruangan');
    }

    public function test_admin_can_add_edit_and_toggle_room(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)->test(Kelola::class)
            ->set('nama', 'Aula')->set('lokasi', 'Lantai 1')->set('kapasitas', '50')->set('fasilitas', 'Sound system')
            ->call('save')->assertHasNoErrors();

        $room = Room::where('nama', 'Aula')->firstOrFail();
        $this->assertSame(50, $room->kapasitas);

        Livewire::actingAs($admin)->test(Kelola::class)
            ->call('edit', $room->id)
            ->assertSet('nama', 'Aula')
            ->set('kapasitas', '60')
            ->call('update')->assertHasNoErrors();
        $this->assertSame(60, $room->fresh()->kapasitas);

        Livewire::actingAs($admin)->test(Kelola::class)->call('toggleStatus', $room->id);
        $this->assertSame('tidak_tersedia', $room->fresh()->status);
        Livewire::actingAs($admin)->test(Kelola::class)->call('toggleStatus', $room->id);
        $this->assertSame('tersedia', $room->fresh()->status);
    }

    public function test_duplicate_room_name_is_rejected_but_own_name_is_allowed_on_edit(): void
    {
        $admin = $this->admin();
        $a = $this->room(['nama' => 'Ruang A']);
        $this->room(['nama' => 'Ruang B']);

        Livewire::actingAs($admin)->test(Kelola::class)
            ->set('nama', 'Ruang A')->set('kapasitas', '5')->call('save')
            ->assertHasErrors(['nama']);

        Livewire::actingAs($admin)->test(Kelola::class)
            ->call('edit', $a->id)->set('kapasitas', '7')->call('update')
            ->assertHasNoErrors();
    }

    public function test_deactivated_room_is_not_offered_for_booking(): void
    {
        $room = $this->room(['status' => 'tidak_tersedia']);

        Livewire::actingAs($this->pegawai())->test(Index::class)
            ->set('roomId', (string) $room->id)->set('keperluan', 'Rapat koordinasi')->set('jumlahPeserta', '2')
            ->set('tanggalMulai', now()->addDay()->format('Y-m-d\TH:i'))
            ->set('tanggalSelesai', now()->addDay()->addHour()->format('Y-m-d\TH:i'))
            ->call('submit')->assertHasErrors(['roomId']);
    }
    // ---- Edit, Hapus, dan aksi di semua status ----

    public function test_reviewer_can_change_decision_after_processing(): void
    {
        $room = $this->room();
        $b = $this->booking($room, $this->pegawai(), now()->addDay()->setTime(9, 0), now()->addDay()->setTime(10, 0), 'ditolak');

        $c = Livewire::actingAs($this->admin())->test(Index::class, ['mode' => 'reviewer']);
        $c->call('approve', $b->id);
        $this->assertSame('disetujui', $b->fresh()->status);

        $c->call('reject', $b->id);
        $this->assertSame('ditolak', $b->fresh()->status);
    }

    public function test_cancelled_booking_cannot_be_approved_or_rejected(): void
    {
        $b = $this->booking($this->room(), $this->pegawai(), now()->addDay(), now()->addDay()->addHour(), 'dibatalkan');

        Livewire::actingAs($this->admin())->test(Index::class, ['mode' => 'reviewer'])
            ->call('approve', $b->id)->call('reject', $b->id);

        $this->assertSame('dibatalkan', $b->fresh()->status);
    }

    public function test_reviewer_can_edit_booking_of_any_status(): void
    {
        $room = $this->room(['kapasitas' => 20]);
        $b = $this->booking($room, $this->pegawai(), now()->addDay()->setTime(9, 0), now()->addDay()->setTime(10, 0), 'disetujui');

        Livewire::actingAs($this->admin())->test(Index::class, ['mode' => 'reviewer'])
            ->call('edit', $b->id)
            ->assertSet('keperluan', 'Rapat koordinasi')
            ->set('keperluan', 'Rapat koordinasi diperbarui')
            ->set('jumlahPeserta', '15')
            ->call('update')->assertHasNoErrors();

        $b->refresh();
        $this->assertSame('Rapat koordinasi diperbarui', $b->keperluan);
        $this->assertSame(15, $b->jumlah_peserta);
        $this->assertSame('disetujui', $b->status);
    }

    public function test_edit_is_blocked_when_it_overlaps_another_booking(): void
    {
        $room = $this->room();
        $u = $this->pegawai();
        $this->booking($room, $u, now()->addDay()->setTime(9, 0), now()->addDay()->setTime(11, 0), 'disetujui');
        $b = $this->booking($room, $u, now()->addDay()->setTime(13, 0), now()->addDay()->setTime(14, 0));

        Livewire::actingAs($this->admin())->test(Index::class, ['mode' => 'reviewer'])
            ->call('edit', $b->id)
            ->set('tanggalMulai', now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'))
            ->set('tanggalSelesai', now()->addDay()->setTime(12, 0)->format('Y-m-d\TH:i'))
            ->call('update')->assertHasErrors(['tanggalMulai']);
    }

    public function test_pemohon_can_edit_only_own_pending_and_delete_only_closed(): void
    {
        $room = $this->room();
        $owner = $this->pegawai();
        $other = $this->pegawai();
        $pending = $this->booking($room, $owner, now()->addDay()->setTime(9, 0), now()->addDay()->setTime(10, 0));
        $approved = $this->booking($room, $owner, now()->addDays(2)->setTime(9, 0), now()->addDays(2)->setTime(10, 0), 'disetujui');
        $rejected = $this->booking($room, $owner, now()->addDays(3)->setTime(9, 0), now()->addDays(3)->setTime(10, 0), 'ditolak');

        // orang lain tidak bisa mengedit / menghapus
        Livewire::actingAs($other)->test(Index::class)->call('edit', $pending->id)->assertSet('editId', null);
        Livewire::actingAs($other)->test(Index::class)->call('delete', $rejected->id);
        $this->assertDatabaseHas('room_bookings', ['id' => $rejected->id]);

        // pemilik: edit pending boleh, edit disetujui tidak
        Livewire::actingAs($owner)->test(Index::class)->call('edit', $pending->id)->assertSet('editId', $pending->id);
        Livewire::actingAs($owner)->test(Index::class)->call('edit', $approved->id)->assertSet('editId', null);

        // pemilik: hapus ditolak boleh, hapus disetujui/pending tidak
        Livewire::actingAs($owner)->test(Index::class)->call('delete', $approved->id)->call('delete', $pending->id);
        $this->assertDatabaseHas('room_bookings', ['id' => $approved->id]);
        $this->assertDatabaseHas('room_bookings', ['id' => $pending->id]);
        Livewire::actingAs($owner)->test(Index::class)->call('delete', $rejected->id);
        $this->assertDatabaseMissing('room_bookings', ['id' => $rejected->id]);
    }

    public function test_reviewer_can_delete_booking_of_any_status(): void
    {
        $b = $this->booking($this->room(), $this->pegawai(), now()->addDay(), now()->addDay()->addHour(), 'disetujui');

        Livewire::actingAs($this->admin())->test(Index::class, ['mode' => 'reviewer'])->call('delete', $b->id);

        $this->assertDatabaseMissing('room_bookings', ['id' => $b->id]);
    }
}
