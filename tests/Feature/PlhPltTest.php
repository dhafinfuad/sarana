<?php

namespace Tests\Feature;

use App\Models\Permisi;
use App\Models\PlhPlt;
use App\Models\User;
use Tests\TestCase;

class PlhPltTest extends TestCase
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

    public function test_mujiburrokhman_is_recognized_as_plt_kepala_seksi_pelayanan(): void
    {
        $muji = User::find(140);
        $this->assertNotNull($muji);

        // Seksi definitif
        $this->assertEquals('Seksi Pemeriksaan, Penilaian, dan Penagihan', $muji->seksi);
        $this->assertEquals('Kepala Seksi', $muji->jabatan);

        // PLT Aktif
        $activePlts = $muji->activePlhPlt;
        $this->assertTrue($activePlts->contains('posisi', 'Seksi Pelayanan'));

        // Managed seksi mencakup seksi definitif dan seksi PLT
        $managedSeksi = $muji->getManagedSeksiList();
        $this->assertContains('Seksi Pemeriksaan, Penilaian, dan Penagihan', $managedSeksi);
        $this->assertContains('Seksi Pelayanan', $managedSeksi);

        $this->assertTrue($muji->isKepalaSeksi());
        $this->assertContains('PLT Kepala Seksi Pelayanan', $muji->getPlhPltBadges());
    }

    public function test_mujiburrokhman_can_access_pelayanan_permisi(): void
    {
        $muji = User::find(140);
        $managedSeksi = $muji->getManagedSeksiList();

        // Cari permohonan dari pegawai Seksi Pelayanan
        $pelayananPermisi = Permisi::whereHas('user', function ($q) use ($managedSeksi) {
            $q->whereIn('seksi', $managedSeksi)->where('jabatan', '!=', 'Kepala Seksi');
        })->whereHas('user', function ($q) {
            $q->where('seksi', 'Seksi Pelayanan');
        })->get();

        $this->assertNotEmpty($pelayananPermisi);
    }

    public function test_livewire_permisi_dashboard_reviewer_mode_shows_pelayanan_records(): void
    {
        $muji = User::find(140);
        $this->actingAs($muji);

        \Livewire\Livewire::test(\App\Livewire\Permisi\Dashboard::class)
            ->set('isReviewerMode', true)
            ->assertStatus(200)
            ->assertSee('Akrim Yazid Isninanda')
            ->assertSee('Wendra Rayudianto');
    }

    public function test_plt_kepala_kantor_access_rights(): void
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            PlhPlt::create([
                'posisi' => 'Kepala Kantor',
                'jenis' => 'PLT',
                'pegawai_id' => 140,
                'tanggal_mulai' => '2026-09-01',
                'status_aktif' => 1,
            ]);

            $muji = User::find(140);
            $this->assertTrue($muji->isKepalaKantor());
            $this->assertTrue($muji->canAccessAdminDashboard());
            $this->assertContains('PLT Kepala Kantor', $muji->getPlhPltBadges());
        } finally {
            \Illuminate\Support\Facades\DB::rollBack();
        }
    }
}
