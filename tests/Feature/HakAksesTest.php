<?php

namespace Tests\Feature;

use App\Livewire\Admin\HakAkses as HakAksesPage;
use App\Models\ActivityLog;
use App\Models\PlhPlt;
use App\Models\User;
use App\Support\HakAkses;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class HakAksesTest extends TestCase
{
    use RefreshDatabase;

    private static int $seq = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // Tabel plh_plt tidak punya migrasi di repo (hanya ada di DB produksi); buat versi minimal.
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
        HakAkses::flush();
    }

    private function user(string $jabatan = 'Pelaksana', ?string $seksi = 'Seksi Pelayanan'): User
    {
        return User::forceCreate([
            'name' => 'Tester ' . (++self::$seq),
            'nip_pendek' => str_pad((string) (700000000 + self::$seq), 9, '0'),
            'jabatan' => $jabatan, 'seksi' => $seksi, 'password' => 'password',
        ]);
    }

    private function plt(User $u, string $posisi): void
    {
        PlhPlt::create([
            'posisi' => $posisi, 'jenis' => 'PLT', 'pegawai_id' => $u->id, 'status_aktif' => 1,
            'tanggal_mulai' => now()->subDay()->toDateString(), 'tanggal_selesai' => now()->addMonth()->toDateString(),
        ]);
    }

    /** Aturan lama (sebelum matriks), dipakai sebagai pembanding. */
    private function legacy(User $u): array
    {
        $kk = $u->jabatan === 'Kepala Kantor';
        $adm = $u->jabatan === 'Administrator';
        $su = $u->isSubbagUmum();

        return [
            'permisi.persetujuan' => $u->isKepalaKantor() || $u->isKepalaSeksi() || $u->isSupervisor() || $su || $adm,
            'permisi.permohonan'  => ! $kk,
            'kendaraan.peminjaman' => ! $kk,
            'ruangan.peminjaman'  => ! $kk,
            'kendaraan.armada.kelola' => ! $kk && ($adm || $su),
            'kendaraan.dashboard.aksi' => ! $kk && ($adm || $su),
            'ruangan.persetujuan' => ! $kk && ($adm || $su),
            'ruangan.kelola'      => ! $kk && ($adm || $su),
            'bona.kelola'         => $su || $adm,
            'bona.master'         => $su || $adm,
            'asmara.permohonan'   => true,
            'asmara.admin'        => $adm || $su || $u->seksi === 'Seksi Penjaminan Kualitas Data',
        ];
    }

    /** @return array<string, User> */
    private function profiles(): array
    {
        $plt = $this->user('Pelaksana', 'Seksi Pelayanan');
        $this->plt($plt, 'Kepala Kantor');
        $pltSeksi = $this->user('Pelaksana', 'Seksi Umum');
        $this->plt($pltSeksi, 'Kepala Seksi Pelayanan');
        $mergedSeksi = $this->user('Kepala Seksi', 'Seksi Pelayanan');

        return [
            'Administrator' => $this->user('Administrator', null),
            'Kepala Kantor' => $this->user('Kepala Kantor', null),
            'PLT Kepala Kantor' => $plt,
            'Kepala Seksi' => $mergedSeksi,
            'PLT Kepala Seksi' => $pltSeksi,
            'Supervisor' => $this->user('Supervisor'),
            'Subbag Umum' => $this->user('Pelaksana', 'Subbagian Umum dan Kepatuhan Internal'),
            'PKD' => $this->user('Pelaksana', 'Seksi Penjaminan Kualitas Data'),
            'Pelaksana' => $this->user('Pelaksana'),
            'FPP' => $this->user('FPP'),
        ];
    }

    public function test_default_matrix_equals_legacy_rules_for_every_profile(): void
    {
        foreach ($this->profiles() as $label => $u) {
            foreach ($this->legacy($u) as $key => $expected) {
                $this->assertSame($expected, $u->hasAccess($key), "$label / $key");
            }
            // method lama tetap bekerja (dialihkan ke matriks)
            $this->assertSame($this->legacy($u)['permisi.permohonan'], $u->canAccessPermohonanPermisi(), "$label / canAccessPermohonanPermisi");
            $this->assertSame($this->legacy($u)['bona.kelola'], $u->canAccessBonKelola(), "$label / canAccessBonKelola");
        }
    }

    public function test_agreed_behaviour_changes(): void
    {
        $p = $this->profiles();

        // Dashboard & Manajemen Armada: Kepala Kantor tidak lagi, hanya Administrator dan Subbag Umum
        $this->assertFalse($p['Kepala Kantor']->canAccessAdminDashboard());
        $this->assertFalse($p['Kepala Kantor']->hasAccess('kendaraan.armada'));
        $this->assertTrue($p['Administrator']->canAccessAdminDashboard());
        $this->assertTrue($p['Subbag Umum']->hasAccess('kendaraan.armada'));
        $this->assertFalse($p['Supervisor']->canAccessAdminDashboard());

        // PLT Kepala Kantor kini boleh Bon ATK (sama dengan Permisi dan Peminjaman); definitif tetap tidak
        $this->assertTrue($p['PLT Kepala Kantor']->canAccessBonPermohonan());
        $this->assertFalse($p['Kepala Kantor']->canAccessBonPermohonan());

        // Manajemen User dan Log: Administrator saja
        foreach (['admin.user', 'admin.log', 'admin.hak_akses'] as $k) {
            foreach ($p as $label => $u) {
                $this->assertSame($label === 'Administrator', $u->hasAccess($k), "$label / $k");
            }
        }
    }

    public function test_roles_are_combined_with_or(): void
    {
        $u = $this->user('Kepala Seksi', 'Seksi Pelayanan');
        $this->assertEqualsCanonicalizing(['kepala_seksi', 'pegawai'], $u->roles());

        // Persetujuan Ruangan hanya diberikan ke "Kepala Seksi" -> orang ini boleh; Pelaksana biasa tidak
        $m = HakAkses::matrix();
        $m['ruangan.persetujuan']['kepala_seksi'] = true;
        HakAkses::save($m, $this->user('Administrator', null));

        $this->assertTrue($u->hasAccess('ruangan.persetujuan'));
        $this->assertFalse($this->user()->hasAccess('ruangan.persetujuan'));
    }

    public function test_pegawai_role_excludes_only_definitive_kepala_kantor(): void
    {
        $this->assertNotContains('pegawai', $this->user('Kepala Kantor', null)->roles());
        $this->assertContains('pegawai', $this->user('Administrator', null)->roles());
        $plt = $this->user();
        $this->plt($plt, 'Kepala Kantor');
        // PLT Kepala Kantor: Kepala Kantor + Pegawai (isKepalaSeksi() lama juga menganggap setiap PLH/PLT aktif sebagai pemimpin seksi)
        $this->assertContains('kepala_kantor', $plt->roles());
        $this->assertContains('pegawai', $plt->roles());
    }

    public function test_save_only_stores_differences_from_default_and_takes_effect_immediately(): void
    {
        $admin = $this->user('Administrator', null);
        $pegawai = $this->user();
        $this->assertFalse($pegawai->hasAccess('ruangan.persetujuan'));

        $m = HakAkses::matrix();
        $m['ruangan.persetujuan']['pegawai'] = true;      // berbeda dari bawaan
        $m['bona.kelola']['subbag_umum'] = true;           // sama dengan bawaan
        $changes = HakAkses::save($m, $admin);

        $this->assertCount(1, $changes);
        $this->assertSame(1, DB::table('role_permissions')->count());
        $this->assertTrue($pegawai->hasAccess('ruangan.persetujuan'));
        $this->actingAs($pegawai)->get('/ruangan/persetujuan')->assertOk();

        // revert
        $m['ruangan.persetujuan']['pegawai'] = false;
        HakAkses::save($m, $admin);
        $this->assertSame(0, DB::table('role_permissions')->count());
        $this->actingAs($pegawai)->get('/ruangan/persetujuan')->assertForbidden();
    }

    public function test_locked_rows_and_administrator_column_cannot_be_changed(): void
    {
        $admin = $this->user('Administrator', null);
        $m = HakAkses::matrix();
        $m['admin.hak_akses']['pegawai'] = true;           // baris terkunci
        $m['ruangan.kelola']['administrator'] = false;     // kolom Administrator
        HakAkses::save($m, $admin);

        $this->assertFalse($this->user()->hasAccess('admin.hak_akses'));
        $this->assertTrue($admin->hasAccess('ruangan.kelola'));
        $this->assertTrue($admin->hasAccess('admin.hak_akses'));
        $this->assertSame(0, DB::table('role_permissions')->count());
    }

    public function test_reset_restores_defaults(): void
    {
        $admin = $this->user('Administrator', null);
        $m = HakAkses::matrix();
        $m['ruangan.persetujuan']['pegawai'] = true;
        $m['bona.kelola']['subbag_umum'] = false;
        HakAkses::save($m, $admin);
        $this->assertSame(2, DB::table('role_permissions')->count());

        HakAkses::reset($admin);
        $this->assertSame(0, DB::table('role_permissions')->count());
        $this->assertFalse($this->user()->hasAccess('ruangan.persetujuan'));
        $this->assertTrue($this->user('Pelaksana', 'Subbagian Umum dan Kepatuhan Internal')->hasAccess('bona.kelola'));
    }

    public function test_hak_akses_page_is_for_administrator_only(): void
    {
        $this->actingAs($this->user('Administrator', null))->get('/admin/hak-akses')->assertOk()
            ->assertSee('Hak Akses')->assertSee('Cek Pengguna')->assertSee('Simpan Perubahan');

        foreach ([$this->user('Pelaksana', 'Subbagian Umum dan Kepatuhan Internal'), $this->user('Kepala Kantor', null), $this->user('Kepala Seksi'), $this->user('Supervisor'), $this->user()] as $u) {
            $this->actingAs($u)->get('/admin/hak-akses')->assertForbidden();
        }
    }

    public function test_page_saves_changes_and_writes_audit_log(): void
    {
        $admin = $this->user('Administrator', null);
        $slot = HakAksesPage::slot('ruangan.persetujuan');

        $c = Livewire::actingAs($admin)->test(HakAksesPage::class)
            ->set("matrix.$slot.pegawai", true);
        $this->assertSame(1, $c->instance()->dirtyCount());
        $c->call('save');

        $this->assertTrue($this->user()->hasAccess('ruangan.persetujuan'));
        $log = ActivityLog::where('route_name', 'admin.hak-akses.update')->latest('id')->first();
        $this->assertNotNull($log);
        $this->assertSame($admin->id, $log->user_id);
        $this->assertStringContainsString('Pegawai: Persetujuan Ruangan', $log->page_label);

        Livewire::actingAs($admin)->test(HakAksesPage::class)->call('resetDefaults');
        $this->assertFalse($this->user()->hasAccess('ruangan.persetujuan'));
    }

    public function test_non_administrator_cannot_save_through_livewire(): void
    {
        $slot = HakAksesPage::slot('ruangan.persetujuan');
        Livewire::actingAs($this->user('Pelaksana', 'Subbagian Umum dan Kepatuhan Internal'))->test(HakAksesPage::class)
            ->assertForbidden();
        $this->assertSame(0, DB::table('role_permissions')->count());
    }

    public function test_check_user_panel_shows_roles_and_effective_access(): void
    {
        $admin = $this->user('Administrator', null);
        $target = $this->user('Kepala Seksi', 'Seksi Pelayanan');

        $c = Livewire::actingAs($admin)->test(HakAksesPage::class)->call('selectUser', $target->id);
        $checked = $c->instance()->checked();
        $this->assertEqualsCanonicalizing(['Kepala Seksi', 'Pegawai'], $checked['roles']);
        $this->assertContains('Persetujuan Izin', $checked['access']['Permisi']);
        $this->assertArrayNotHasKey('Admin', $checked['access']);
    }

    public function test_menu_shows_hak_akses_link_only_for_administrator(): void
    {
        $url = route('admin.hak-akses', absolute: false);
        $this->actingAs($this->user('Administrator', null))->get('/ruangan/peminjaman')->assertOk()->assertSee($url, false);
        $this->actingAs($this->user('Pelaksana', 'Subbagian Umum dan Kepatuhan Internal'))->get('/ruangan/peminjaman')->assertOk()->assertDontSee($url, false);
    }

    public function test_home_route_follows_access(): void
    {
        $p = $this->profiles();
        $this->assertSame(route('admin.dashboard'), $p['Administrator']->homeRoute());
        $this->assertSame(route('admin.dashboard'), $p['Subbag Umum']->homeRoute());
        $this->assertSame(route('peminjaman.index'), $p['Pelaksana']->homeRoute());
        $this->assertSame(route('permisi.persetujuan'), $p['Kepala Kantor']->homeRoute());   // definitif tidak boleh peminjaman
    }

    public function test_unknown_key_is_denied_and_catalog_is_consistent(): void
    {
        $this->assertFalse($this->user('Administrator', null)->hasAccess('tidak.ada'));
        foreach (HakAkses::catalog() as $key => $meta) {
            foreach ($meta['default'] as $role) {
                $this->assertArrayHasKey($role, HakAkses::roles(), "$key memakai peran tak dikenal $role");
            }
        }
    }
}
