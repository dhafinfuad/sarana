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

    public function test_actions_visible_only_for_pending_unless_privileged_role(): void
    {
        $u = $this->user();
        $this->permisi($u, 'pending', 2);
        $this->permisi($u, 'disetujui', 1);

        $recent = Livewire::actingAs($u)->test(Dashboard::class)->instance()->recent();
        $this->assertTrue((bool) $recent->firstWhere('status', 'pending')->show_actions);
        $this->assertFalse((bool) $recent->firstWhere('status', 'disetujui')->show_actions);

        $kepala = $this->user('Kepala Seksi');
        $this->permisi($kepala, 'disetujui');
        $this->assertTrue((bool) Livewire::actingAs($kepala)->test(Dashboard::class)->instance()->recent()->first()->show_actions);
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

    public function test_privileged_roles_can_delete_and_edit_own_request_of_any_status(): void
    {
        foreach (['Kepala Seksi', 'Administrator'] as $jabatan) {
            $u = $this->user($jabatan);
            $p = $this->permisi($u, 'disetujui');

            Livewire::actingAs($u)->test(Dashboard::class)->call('editBooking', $p->id)->assertSet('editId', $p->id);
            Livewire::actingAs($u)->test(Dashboard::class)->call('deleteBooking', $p->id);
            $this->assertDatabaseMissing('permisis', ['id' => $p->id]);
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
}
