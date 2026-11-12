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
use Maatwebsite\Excel\Facades\Excel;
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

    public function test_pemohon_can_edit_and_delete_only_own_pending(): void
    {
        $room = $this->room();
        $owner = $this->pegawai();
        $other = $this->pegawai();
        $pending = $this->booking($room, $owner, now()->addDay()->setTime(9, 0), now()->addDay()->setTime(10, 0));
        $approved = $this->booking($room, $owner, now()->addDays(2)->setTime(9, 0), now()->addDays(2)->setTime(10, 0), 'disetujui');
        $rejected = $this->booking($room, $owner, now()->addDays(3)->setTime(9, 0), now()->addDays(3)->setTime(10, 0), 'ditolak');

        // orang lain tidak bisa mengedit / menghapus
        Livewire::actingAs($other)->test(Index::class)->call('edit', $pending->id)->assertSet('editId', null);
        Livewire::actingAs($other)->test(Index::class)->call('delete', $pending->id);
        $this->assertDatabaseHas('room_bookings', ['id' => $pending->id]);

        // pemilik: status selain Pending tidak bisa diedit maupun dihapus
        Livewire::actingAs($owner)->test(Index::class)->call('edit', $approved->id)->assertSet('editId', null);
        Livewire::actingAs($owner)->test(Index::class)->call('delete', $approved->id)->call('delete', $rejected->id);
        $this->assertDatabaseHas('room_bookings', ['id' => $approved->id]);
        $this->assertDatabaseHas('room_bookings', ['id' => $rejected->id]);

        // pemilik: Pending boleh diedit dan langsung dihapus
        Livewire::actingAs($owner)->test(Index::class)->call('edit', $pending->id)->assertSet('editId', $pending->id);
        Livewire::actingAs($owner)->test(Index::class)->call('delete', $pending->id);
        $this->assertDatabaseMissing('room_bookings', ['id' => $pending->id]);
    }

    public function test_all_roles_can_only_edit_and_delete_own_pending_booking_on_peminjaman_page(): void
    {
        $room = $this->room();
        foreach (['Kepala Seksi', 'Administrator', 'Pelaksana'] as $i => $jabatan) {
            $u = $this->makeUser(['jabatan' => $jabatan, 'seksi' => 'Seksi Pelayanan']);
            $approved = $this->booking($room, $u, now()->addDays($i * 2 + 1)->setTime(9, 0), now()->addDays($i * 2 + 1)->setTime(10, 0), 'disetujui');
            $pending = $this->booking($room, $u, now()->addDays($i * 2 + 2)->setTime(9, 0), now()->addDays($i * 2 + 2)->setTime(10, 0));

            // status selain Pending: semua peran ditolak (termasuk Administrator di halaman pemohon)
            Livewire::actingAs($u)->test(Index::class)->call('edit', $approved->id)->assertSet('editId', null);
            Livewire::actingAs($u)->test(Index::class)->call('delete', $approved->id);
            $this->assertDatabaseHas('room_bookings', ['id' => $approved->id]);

            // Pending milik sendiri: boleh
            Livewire::actingAs($u)->test(Index::class)->call('edit', $pending->id)->assertSet('editId', $pending->id);
            Livewire::actingAs($u)->test(Index::class)->call('delete', $pending->id);
            $this->assertDatabaseMissing('room_bookings', ['id' => $pending->id]);
        }
    }

    public function test_recent_list_shows_only_five_newest_and_actions_only_for_pending(): void
    {
        $room = $this->room();
        $u = $this->pegawai();
        foreach (range(1, 7) as $i) {
            $this->booking($room, $u, now()->addDays($i)->setTime(9, 0), now()->addDays($i)->setTime(10, 0), $i === 7 ? 'pending' : 'disetujui');
        }

        $c = Livewire::actingAs($u)->test(Index::class);
        $recent = $c->instance()->recent();
        $this->assertCount(5, $recent);
        $this->assertSame(7, $recent->first()->id);                        // terbaru dulu
        $this->assertTrue((bool) $recent->first()->show_actions);          // Pending: aksi tampil
        $this->assertFalse((bool) $recent->last()->show_actions);          // Disetujui: aksi disembunyikan
        $c->assertSee('Lihat Semua Riwayat')->assertDontSee('Semua Status');

        // semua peran sama: hanya Pending yang menampilkan tombol aksi
        $kepala = $this->makeUser(['jabatan' => 'Kepala Seksi', 'seksi' => 'Seksi Pelayanan']);
        $this->booking($room, $kepala, now()->addDays(20)->setTime(9, 0), now()->addDays(20)->setTime(10, 0), 'disetujui');
        $this->assertFalse((bool) Livewire::actingAs($kepala)->test(Index::class)->instance()->recent()->first()->show_actions);
    }

    public function test_riwayat_page_lists_all_with_filter_and_pagination(): void
    {
        $room = $this->room();
        $u = $this->pegawai();
        foreach (range(1, 12) as $i) {
            $this->booking($room, $u, now()->addDays($i)->setTime(9, 0), now()->addDays($i)->setTime(10, 0), $i <= 3 ? 'ditolak' : 'pending');
        }

        $this->actingAs($u)->get('/ruangan/riwayat')->assertOk()->assertSee('Riwayat Peminjaman Ruangan');

        $c = Livewire::actingAs($u)->test(\App\Livewire\Ruangan\Riwayat::class);
        $this->assertSame(12, $c->instance()->bookings()->total());
        $this->assertCount(10, $c->instance()->bookings());
        $c->set('status', 'ditolak');
        $this->assertSame(3, $c->instance()->bookings()->total());
    }

    public function test_riwayat_page_is_forbidden_for_kepala_kantor(): void
    {
        $this->actingAs($this->makeUser(['jabatan' => 'Kepala Kantor']))->get('/ruangan/riwayat')->assertForbidden();
    }

    public function test_reviewer_can_delete_booking_of_any_status(): void
    {
        $b = $this->booking($this->room(), $this->pegawai(), now()->addDay(), now()->addDay()->addHour(), 'disetujui');

        Livewire::actingAs($this->admin())->test(Index::class, ['mode' => 'reviewer'])->call('delete', $b->id);

        $this->assertDatabaseMissing('room_bookings', ['id' => $b->id]);
    }
    public function test_persetujuan_kpi_follow_the_table_filters(): void
    {
        $utama = $this->room(['nama' => 'Ruang Utama', 'kapasitas' => 12]);
        $kecil = $this->room(['nama' => 'Ruang Kecil', 'kapasitas' => 4]);
        $a = $this->makeUser(['jabatan' => 'Pelaksana', 'seksi' => 'Seksi Pelayanan', 'name' => 'Andi']);
        $b = $this->makeUser(['jabatan' => 'Pelaksana', 'seksi' => 'Seksi Umum', 'name' => 'Budi']);
        $t = fn (int $d) => now()->addDays($d)->setTime(9, 0);
        $this->booking($utama, $a, $t(1), $t(1)->copy()->addHour(), 'pending');
        $this->booking($utama, $a, $t(2), $t(2)->copy()->addHour(), 'disetujui');
        $this->booking($utama, $a, $t(3), $t(3)->copy()->addHour(), 'disetujui');
        $this->booking($kecil, $b, $t(4), $t(4)->copy()->addHour(), 'ditolak');

        $c = Livewire::actingAs($this->admin())->test(Index::class, ['mode' => 'reviewer']);
        $k = $c->instance()->analytics();
        $this->assertSame(4, $k['total']);
        $this->assertSame(['Ruang Utama', 3], [$k['topRoom']->name, (int) $k['topRoom']->total]);
        $this->assertSame(12, (int) $k['topRoom']->kapasitas);
        $this->assertSame(['Seksi Pelayanan', 3], [$k['topSeksi']->name, (int) $k['topSeksi']->total]);
        $this->assertSame(['Andi', 3], [$k['topPegawai']->name, (int) $k['topPegawai']->total]);
        $this->assertSame(1, $k['pending']);

        // KPI mengikuti filter tabel (status), lencana "Menunggu" tidak
        $c->set('status', 'ditolak');
        $k = $c->instance()->analytics();
        $this->assertSame(1, $k['total']);
        $this->assertSame('Ruang Kecil', $k['topRoom']->name);
        $this->assertSame('Budi', $k['topPegawai']->name);
        $this->assertSame(1, $k['pending']);
    }

    public function test_persetujuan_page_has_dashboard_layout_elements(): void
    {
        $room = $this->room();
        $this->booking($room, $this->pegawai(), now()->addDay()->setTime(9, 0), now()->addDay()->setTime(10, 0));

        $this->actingAs($this->admin())->get('/ruangan/persetujuan')->assertOk()
            ->assertSee('Total Peminjaman')->assertSee('Top Ruangan')->assertSee('Top Seksi')->assertSee('Top Pegawai')
            ->assertSee('Manajemen Peminjaman Ruangan')->assertSee('1 Menunggu')->assertSee('Reset Filter')
            ->assertDontSee('TOTAL PENGAJUAN');
    }

    public function test_persetujuan_date_filter_and_sorting(): void
    {
        $room = $this->room();
        $u = $this->pegawai();
        $early = $this->booking($room, $u, now()->addDays(2)->setTime(9, 0), now()->addDays(2)->setTime(10, 0));
        $late = $this->booking($room, $u, now()->addDays(9)->setTime(9, 0), now()->addDays(9)->setTime(10, 0));

        $c = Livewire::actingAs($this->admin())->test(Index::class, ['mode' => 'reviewer']);
        $this->assertSame($late->id, $c->instance()->bookings()->first()->id);          // default: mulai terbaru dulu

        $c->call('sortBy', 'tanggal_mulai');                                            // klik kolom: asc
        $this->assertSame($early->id, $c->instance()->bookings()->first()->id);

        $c->set('startDate', now()->addDays(5)->toDateString());
        $this->assertSame([$late->id], $c->instance()->bookings()->pluck('id')->all());
        $this->assertSame(1, $c->instance()->analytics()['total']);
    }

    public function test_kpi_cards_are_not_shown_on_peminjaman_page(): void
    {
        Livewire::actingAs($this->pegawai())->test(Index::class)->assertDontSee('TOTAL PENGAJUAN');
    }
    public function test_pemohon_overview_counts_only_own_bookings(): void
    {
        $room = $this->room();
        $me = $this->pegawai();
        $other = $this->pegawai();
        $t = fn (int $h) => now()->addDay()->setTime($h, 0);
        $this->booking($room, $me, $t(8), $t(9), 'pending');
        $this->booking($room, $me, $t(10), $t(11), 'disetujui');
        $this->booking($room, $me, $t(12), $t(13), 'ditolak');
        $this->booking($room, $me, $t(14), $t(15), 'dibatalkan');
        $this->booking($room, $other, $t(16), $t(17), 'pending');

        $c = Livewire::actingAs($me)->test(Index::class);
        $this->assertSame(['pending' => 1, 'disetujui' => 1, 'gagal' => 2], $c->instance()->overview());
        $c->assertSee('Overview Status Peminjaman')->assertSee('Riwayat Peminjaman')->assertDontSee('TOTAL PENGAJUAN');
    }
    // ---- Tombol Peminjaman & Export di halaman Persetujuan ----

    public function test_persetujuan_header_has_peminjaman_and_export_buttons_for_managers(): void
    {
        $this->actingAs($this->admin())->get('/ruangan/persetujuan')->assertOk()
            ->assertSee('showModal = true', false)
            ->assertSee('showExportModal = true', false)
            ->assertSee('Form Pengajuan Peminjaman Ruangan')
            ->assertSee('Export Laporan Peminjaman Ruangan')
            ->assertSee('Unduh Laporan');
    }

    public function test_manager_can_submit_booking_from_persetujuan_page(): void
    {
        $room = $this->room();
        $admin = $this->admin();

        Livewire::actingAs($admin)->test(Index::class, ['mode' => 'reviewer'])
            ->set('roomId', (string) $room->id)->set('keperluan', 'Rapat pimpinan')->set('jumlahPeserta', '4')
            ->set('tanggalMulai', now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i'))
            ->set('tanggalSelesai', now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'))
            ->call('submit')->assertHasNoErrors();

        $this->assertDatabaseHas('room_bookings', ['user_id' => $admin->id, 'keperluan' => 'Rapat pimpinan', 'status' => 'pending']);
    }

    public function test_export_downloads_xlsx_for_the_chosen_period_for_managers_only(): void
    {
        Excel::fake();
        $room = $this->room();
        $u = $this->pegawai();
        $this->booking($room, $u, now()->setDate(2026, 5, 10)->setTime(9, 0), now()->setDate(2026, 5, 10)->setTime(10, 0));
        $this->booking($room, $u, now()->setDate(2026, 7, 10)->setTime(9, 0), now()->setDate(2026, 7, 10)->setTime(10, 0));
        $this->booking($room, $u, now()->setDate(2026, 12, 10)->setTime(9, 0), now()->setDate(2026, 12, 10)->setTime(10, 0));

        $this->actingAs($u)->get('/ruangan/export?start_month=1&start_year=2026&end_month=12&end_year=2026')->assertForbidden();

        // periode terbalik otomatis ditukar (Juli..Mei -> Mei..Juli): 2 data
        $this->actingAs($this->admin())->get('/ruangan/export?start_month=7&start_year=2026&end_month=5&end_year=2026')->assertOk();
        Excel::assertDownloaded('laporan-peminjaman-ruangan-05-2026_sd_07-2026.xlsx', function (\App\Exports\RuanganBookingsExport $e) {
            return $e->query()->count() === 2;
        });
    }
    // ---- Akses Persetujuan / Kelola Ruangan: hanya Administrator dan Subbag Umum ----

    public function test_persetujuan_kelola_and_export_are_limited_to_administrator_and_subbag_umum(): void
    {
        $allowed = [
            'Administrator' => $this->admin(),
            'Subbag Umum'   => $this->makeUser(['jabatan' => 'Pelaksana', 'seksi' => 'Subbagian Umum dan Kepatuhan Internal']),
        ];
        $denied = [
            'Pelaksana'    => $this->pegawai(),
            'FPP'          => $this->makeUser(['jabatan' => 'FPP', 'seksi' => 'Seksi Pelayanan']),
            'Supervisor'   => $this->makeUser(['jabatan' => 'Supervisor', 'seksi' => 'Seksi Pelayanan']),
            'Kepala Seksi' => $this->makeUser(['jabatan' => 'Kepala Seksi', 'seksi' => 'Seksi Pelayanan']),
            'Kepala Kantor' => $this->makeUser(['jabatan' => 'Kepala Kantor']),
        ];

        foreach ($allowed as $label => $user) {
            foreach (['/ruangan/persetujuan', '/ruangan/kelola'] as $url) {
                $this->actingAs($user)->get($url)->assertOk();
            }
        }
        foreach ($denied as $label => $user) {
            foreach (['/ruangan/persetujuan', '/ruangan/kelola', '/ruangan/export'] as $url) {
                $this->actingAs($user)->get($url)->assertForbidden();
            }
        }
    }

    public function test_menu_links_to_persetujuan_and_kelola_only_for_administrator_and_subbag_umum(): void
    {
        $persetujuan = route('ruangan.persetujuan', absolute: false);
        $kelola = route('ruangan.kelola', absolute: false);

        foreach ([$this->admin(), $this->makeUser(['jabatan' => 'Pelaksana', 'seksi' => 'Subbagian Umum dan Kepatuhan Internal'])] as $u) {
            $this->actingAs($u)->get('/ruangan/peminjaman')->assertOk()
                ->assertSee($persetujuan, false)->assertSee($kelola, false);
        }

        foreach ([$this->pegawai(), $this->makeUser(['jabatan' => 'Kepala Seksi', 'seksi' => 'Seksi Pelayanan']), $this->makeUser(['jabatan' => 'Supervisor', 'seksi' => 'Seksi Pelayanan'])] as $u) {
            $this->actingAs($u)->get('/ruangan/peminjaman')->assertOk()
                ->assertDontSee($persetujuan, false)->assertDontSee($kelola, false);
        }
    }
}
