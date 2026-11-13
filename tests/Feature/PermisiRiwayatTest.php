<?php

namespace Tests\Feature;

use App\Livewire\Permisi\Dashboard;
use App\Livewire\Permisi\Riwayat;
use App\Models\Permisi;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class PermisiRiwayatTest extends TestCase
{
    use RefreshDatabase;

    private static int $seq = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // Tabel plh_plt tidak punya migrasi di repo (hanya ada di DB produksi), buat versi minimal.
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

    private function user(string $jabatan = 'Pelaksana'): User
    {
        return User::forceCreate([
            'name' => 'Tester ' . (++self::$seq),
            'nip_pendek' => str_pad((string) (800000000 + self::$seq), 9, '0'),
            'jabatan' => $jabatan,
            'seksi' => 'Seksi Pelayanan',
            'password' => 'password',
        ]);
    }

    private function permisi(User $u, string $status = 'pending', int $day = 1): Permisi
    {
        return Permisi::create([
            'user_id' => $u->id, 'kategori' => 'Kantor', 'jenis' => 'Permohonan', 'keperluan' => 'Keperluan dinas luar',
            'tanggal_mulai' => now()->addDays($day)->setTime(9, 0), 'tanggal_selesai' => now()->addDays($day)->setTime(12, 0),
            'status' => $status,
        ]);
    }

    public function test_recent_list_shows_five_newest_with_overview(): void
    {
        $u = $this->user();
        foreach (range(1, 7) as $i) {
            $this->permisi($u, $i === 7 ? 'pending' : 'ditolak', $i);
        }

        $c = Livewire::actingAs($u)->test(Dashboard::class);
        $recent = $c->instance()->recent();
        $this->assertCount(5, $recent);
        $this->assertSame(7, $recent->first()->id);
        $this->assertSame(['pending' => 1, 'disetujui' => 0, 'gagal' => 6, 'selesai' => 0], $c->instance()->overview());
        $c->assertSee('Lihat Semua Riwayat')->assertSee('Overview Status Permohonan')->assertDontSee('Semua Status');
    }

    public function test_permohonan_page_has_no_action_buttons(): void
    {
        $u = $this->user();
        $this->permisi($u, 'pending', 2);

        Livewire::actingAs($u)->test(Dashboard::class)
            ->assertDontSee('Batalkan Permohonan')->assertSee('Detail');
    }

    public function test_delete_rules_for_pemohon(): void
    {
        $u = $this->user();
        $other = $this->user();
        $pending = $this->permisi($u, 'pending', 1);
        $done = $this->permisi($u, 'ditolak', 2);

        Livewire::actingAs($other)->test(Dashboard::class)->call('deleteBooking', $pending->id);
        $this->assertDatabaseHas('permisis', ['id' => $pending->id]);

        Livewire::actingAs($u)->test(Dashboard::class)->call('deleteBooking', $done->id);
        $this->assertDatabaseHas('permisis', ['id' => $done->id]);          // bukan Pending: tidak boleh

        Livewire::actingAs($u)->test(Dashboard::class)->call('deleteBooking', $pending->id);
        $this->assertDatabaseMissing('permisis', ['id' => $pending->id]);   // Pending: langsung boleh
    }

    public function test_every_role_can_only_edit_and_delete_own_pending_request(): void
    {
        foreach (['Kepala Seksi', 'Administrator', 'Pelaksana'] as $jabatan) {
            $u = $this->user($jabatan);
            $done = $this->permisi($u, 'disetujui');
            $pending = $this->permisi($u, 'pending', 3);

            Livewire::actingAs($u)->test(Dashboard::class)->call('editBooking', $done->id)->assertSet('editId', null);
            Livewire::actingAs($u)->test(Dashboard::class)->call('deleteBooking', $done->id);
            $this->assertDatabaseHas('permisis', ['id' => $done->id]);

            Livewire::actingAs($u)->test(Dashboard::class)->call('editBooking', $pending->id)->assertSet('editId', $pending->id);
            Livewire::actingAs($u)->test(Dashboard::class)->call('deleteBooking', $pending->id);
            $this->assertDatabaseMissing('permisis', ['id' => $pending->id]);
        }
    }

    public function test_riwayat_page_lists_all_with_filter_and_pagination(): void
    {
        $u = $this->user();
        foreach (range(1, 12) as $i) {
            $this->permisi($u, $i <= 4 ? 'selesai' : 'pending', $i);
        }

        $this->actingAs($u)->get('/permisi/riwayat')->assertOk()->assertSee('Riwayat Permohonan Izin');

        $c = Livewire::actingAs($u)->test(Riwayat::class);
        $this->assertSame(12, $c->instance()->permisis()->total());
        $this->assertCount(10, $c->instance()->permisis());
        $c->set('status', 'selesai');
        $this->assertSame(4, $c->instance()->permisis()->total());
    }

    public function test_riwayat_page_is_forbidden_for_kepala_kantor(): void
    {
        $this->actingAs($this->user('Kepala Kantor'))->get('/permisi/riwayat')->assertForbidden();
    }
    public function test_persetujuan_page_has_dashboard_layout_and_kpi_follow_filters(): void
    {
        $admin = $this->user('Administrator');
        $a = $this->user('Pelaksana');
        $b = $this->user('Pelaksana');
        $b->update(['seksi' => 'Seksi Umum']);
        $this->permisi($a, 'pending', 1);
        $this->permisi($a, 'disetujui', 2);
        $this->permisi($a, 'disetujui', 3);
        $this->permisi($b, 'ditolak', 4)->update(['kategori' => 'Kesehatan']);

        $this->actingAs($admin)->get('/permisi/persetujuan')->assertOk()
            ->assertSee('Total Permohonan')->assertSee('Top Kategori')->assertSee('Top Seksi')->assertSee('Top Pegawai')
            ->assertSee('Manajemen Permohonan Izin')->assertSee('1 Menunggu')->assertDontSee('TOTAL PERMOHONAN');

        $c = Livewire::actingAs($admin)->test(Dashboard::class)->set('isReviewerMode', true);
        $k = $c->instance()->analytics();
        $this->assertSame(4, $k['total']);
        $this->assertSame(['Kantor', 3], [$k['topKategori']->name, (int) $k['topKategori']->total]);
        $this->assertSame(['Seksi Pelayanan', 3], [$k['topSeksi']->name, (int) $k['topSeksi']->total]);
        $this->assertSame($a->name, $k['topPegawai']->name);
        $this->assertSame(1, $k['pending']);

        $c->set('status', 'ditolak');
        $k = $c->instance()->analytics();
        $this->assertSame(1, $k['total']);
        $this->assertSame('Kesehatan', $k['topKategori']->name);
        $this->assertSame(1, $k['pending']);
    }
    // ---- Tombol Permohonan & Export di halaman Persetujuan ----

    public function test_persetujuan_buttons_depend_on_role(): void
    {
        // Administrator: Permohonan + Export
        $this->actingAs($this->user('Administrator'))->get('/permisi/persetujuan')->assertOk()
            ->assertSee('showApplicationModal = true', false)->assertSee('showExportModal = true', false)
            ->assertSee('Form Pengajuan Permohonan Izin')->assertSee('Export Laporan Permohonan Izin');

        // Kepala Seksi: Permohonan saja, tanpa Export
        $this->actingAs($this->user('Kepala Seksi'))->get('/permisi/persetujuan')->assertOk()
            ->assertSee('showApplicationModal = true', false)->assertDontSee('showExportModal = true', false)
            ->assertDontSee('Export Laporan Permohonan Izin');

        // Kepala Kantor definitif: tidak mengajukan dan tidak mengekspor
        $this->actingAs($this->user('Kepala Kantor'))->get('/permisi/persetujuan')->assertOk()
            ->assertDontSee('showApplicationModal = true', false)->assertDontSee('showExportModal = true', false);
    }

    public function test_reviewer_can_submit_own_permohonan_from_persetujuan_page(): void
    {
        $kepala = $this->user('Kepala Seksi');
        Livewire::actingAs($kepala)->test(Dashboard::class)->set('isReviewerMode', true)
            ->set('kategori', 'Kantor')->set('jenis', 'Permohonan')->set('keperluan', 'Rapat di luar kantor')
            ->set('tanggalMulai', now()->addDay()->setTime(9, 0)->format('Y-m-d\TH:i'))
            ->set('tanggalSelesai', now()->addDay()->setTime(12, 0)->format('Y-m-d\TH:i'))
            ->call('submitApplication')->assertHasNoErrors();
        $this->assertDatabaseHas('permisis', ['user_id' => $kepala->id, 'status' => 'pending', 'keperluan' => 'Rapat di luar kantor']);
    }

    public function test_export_downloads_xlsx_only_for_admin_and_subbag(): void
    {
        Excel::fake();
        $u = $this->user();
        foreach ([5, 7, 12] as $m) {
            Permisi::create([
                'user_id' => $u->id, 'kategori' => 'Kantor', 'jenis' => 'Permohonan', 'keperluan' => 'Dinas',
                'tanggal_mulai' => now()->setDate(2026, $m, 10)->setTime(9, 0), 'tanggal_selesai' => now()->setDate(2026, $m, 10)->setTime(12, 0), 'status' => 'pending',
            ]);
        }

        $url = '/permisi/export?start_month=5&start_year=2026&end_month=7&end_year=2026';
        $this->actingAs($u)->get($url)->assertForbidden();
        $this->actingAs($this->user('Kepala Seksi'))->get($url)->assertForbidden();

        $this->actingAs($this->user('Administrator'))->get($url)->assertOk();
        Excel::assertDownloaded('laporan-permohonan-izin-05-2026_sd_07-2026.xlsx', function (\App\Exports\PermisiExport $e) {
            return $e->query()->count() === 2;
        });
    }
}
