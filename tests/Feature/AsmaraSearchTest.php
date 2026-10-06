<?php

namespace Tests\Feature;

use App\Models\SuratKeluar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsmaraSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_by_partial_and_full_nomor_surat()
    {
        $user = User::create([
            'nip_pendek' => '808320114',
            'nip' => '198803262009121001',
            'name' => 'Dani Sulistiono',
            'password' => bcrypt('password'),
        ]);

        SuratKeluar::create([
            'jenis_surat' => 'S-',
            'nomor_surat' => 276,
            'jenis_pj' => '/KPP.1209/PaPj.2/',
            'tahun_surat' => 2026,
            'tgl_surat' => '2026-09-30',
            'perihal' => 'Peringatan I Peminjaman Dokumen Tujuh Impian Bersama 2023',
            'tujuan_surat' => 'Tujuh Impian Bersama',
            'perekam' => '808320114',
            'tgl_rekam' => '2026-09-30',
        ]);

        // 1. Search by S-276 (exact problem in user screenshot)
        $result = SuratKeluar::search('S-276')->get();
        $this->assertCount(1, $result);
        $this->assertEquals('S-276/KPP.1209/PaPj.2/2026', $result->first()->nomorLengkap);

        // 2. Search by raw number 276
        $result = SuratKeluar::search('276')->get();
        $this->assertCount(1, $result);

        // 3. Search by partial pj
        $result = SuratKeluar::search('PaPj.2')->get();
        $this->assertCount(1, $result);

        // 4. Search by full nomor surat
        $result = SuratKeluar::search('S-276/KPP.1209/PaPj.2/2026')->get();
        $this->assertCount(1, $result);

        // 5. Search by perihal
        $result = SuratKeluar::search('Peminjaman Dokumen')->get();
        $this->assertCount(1, $result);

        // 6. Search by tujuan
        $result = SuratKeluar::search('Tujuh Impian')->get();
        $this->assertCount(1, $result);

        // 7. Search by konseptor name
        $result = SuratKeluar::search('Dani Sulistiono')->get();
        $this->assertCount(1, $result);

        // 8. Search without hyphen (e.g. S276)
        $result = SuratKeluar::search('S276')->get();
        $this->assertCount(1, $result);

        // 9. Search with spaces (e.g. S - 276)
        $result = SuratKeluar::search('S - 276')->get();
        $this->assertCount(1, $result);

        // 10. Search by 18-digit NIP
        $result = SuratKeluar::search('198803262009121001')->get();
        $this->assertCount(1, $result);

        // 11. Search by prefix like in screenshot (e.g. S-2/KPP)
        $result = SuratKeluar::search('S-276/KPP')->get();
        $this->assertCount(1, $result);
    }

    public function test_livewire_dashboard_search_functionality()
    {
        $admin = User::create([
            'nip_pendek' => '123456789',
            'name' => 'Administrator',
            'jabatan' => 'Administrator',
            'password' => bcrypt('password'),
        ]);

        SuratKeluar::create([
            'jenis_surat' => 'S-',
            'nomor_surat' => 276,
            'jenis_pj' => '/KPP.1209/PaPj.2/',
            'tahun_surat' => 2026,
            'tgl_surat' => '2026-09-30',
            'perihal' => 'Peringatan I Peminjaman Dokumen Tujuh Impian Bersama 2023',
            'tujuan_surat' => 'Tujuh Impian Bersama',
            'perekam' => '808320114',
            'tgl_rekam' => '2026-09-30',
        ]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Asmara\Dashboard::class, ['isAdminMode' => true])
            ->set('isAdminMode', true)
            ->set('filterTahun', '2026')
            ->set('search', 'S-276')
            ->assertSee('S-276/KPP.1209/PaPj.2/2026')
            ->assertSee('Peringatan I Peminjaman Dokumen')
            ->assertDontSee('Tidak ada data surat keluar')
            ->set('search', 'NomorYangPastiTidakAda99999')
            ->assertSee('Tidak ada data surat keluar');
    }
}
