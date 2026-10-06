<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignBonItemImagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Assign Gambar ke Barang ATK ===');

        // Mapping: kode_barang => nama file gambar (tanpa path prefix)
        $mapping = [
            // ── KERTAS ────────────────────────────────────────────────────────
            'BRG000154' => 'bon-items/kertas.png', // Kertas Buffalo HVS A4 Merah
            'BRG000155' => 'bon-items/kertas.png', // Kertas Buffalo HVS A4 Biru
            'BRG000157' => 'bon-items/kertas.png', // Kertas HVS 70gr A4 Sidu
            'BRG000158' => 'bon-items/kertas.png', // Kertas HVS 70gr F4 Sidu

            // ── AMPLOP ────────────────────────────────────────────────────────
            'BRG000168' => 'bon-items/amplop.png', // AMPLOP 90 PAPERLINE BESAR
            'BRG000171' => 'bon-items/amplop.png', // Amplop Dinas F4 Putih
            'BRG000172' => 'bon-items/amplop.png', // AMPLOP 110 PAPERLINE KECIL
            'BRG000174' => 'bon-items/amplop.png', // Amplop Dinas F4 Coklat
            'BRG000175' => 'bon-items/amplop.png', // Amplop Dinas A4 Coklat
            'BRG000176' => 'bon-items/amplop.png', // AMPLOP NON KACA KECIL PUTIH
            'BRG000177' => 'bon-items/amplop.png', // Paperline Amplop EV-110 PPS
            'BRG000178' => 'bon-items/amplop.png', // AMPLOP COKLAT KACA KECIL
            'BRG000186' => 'bon-items/amplop.png', // AMPLOP COKLAT KACA PANJANG
            'BRG000228' => 'bon-items/amplop.png', // AMPLOP KACA KECIL PUTIH
            'BRG000229' => 'bon-items/amplop.png', // Amplop Dinas A4 Putih

            // ── BOLPOINT / PENA ───────────────────────────────────────────────
            'BRG000098' => 'bon-items/bolpoint.png', // BOLPOINT BAOKE 1.0 MM
            'BRG000099' => 'bon-items/bolpoint.png', // BOLPOINT SNOWMAN MERAH
            'BRG000100' => 'bon-items/bolpoint.png', // BOLPOINT BOLDLINER HITAM
            'BRG000101' => 'bon-items/bolpoint.png', // BOLPOINT BOLDLINER BLUE
            'BRG000102' => 'bon-items/bolpoint.png', // BOLPOINT BIRU PICK KNOCK
            'BRG000103' => 'bon-items/bolpoint.png', // BOLPOINT BIRU GEL ZEBRA SARASA
            'BRG000104' => 'bon-items/bolpoint.png', // BOLPOINT HITAM PIC KNOCK
            'BRG000105' => 'bon-items/bolpoint.png', // BOLPOINT HITAM JOYCO-GP 279
            'BRG000106' => 'bon-items/bolpoint.png', // BOLPOINT HITAM KOKORO SWEET
            'BRG000140' => 'bon-items/bolpoint.png', // Balliner Pilot Merah
            'BRG000141' => 'bon-items/bolpoint.png', // BALLINER PILOT BIRU
            'BRG000142' => 'bon-items/bolpoint.png', // BALLINER PILOT HITAM

            // ── STABILO ───────────────────────────────────────────────────────
            'BRG000108' => 'bon-items/stabilo.png', // STABILO BIRU
            'BRG000109' => 'bon-items/stabilo.png', // STABILO UNGU
            'BRG000110' => 'bon-items/stabilo.png', // STABILO KUNING (1)
            'BRG000111' => 'bon-items/stabilo.png', // STABILO ABU ABU
            'BRG000112' => 'bon-items/stabilo.png', // STABILO ORANGE
            'BRG000113' => 'bon-items/stabilo.png', // STABILO PINK
            'BRG000114' => 'bon-items/stabilo.png', // STABILO KUNING (2)
            'BRG000115' => 'bon-items/stabilo.png', // STABILO HIJAU

            // ── SPIDOL ────────────────────────────────────────────────────────
            'BRG000082' => 'bon-items/spidol.png', // SPIDOL NON PERMANENT ARTLINE BIRU
            'BRG000083' => 'bon-items/spidol.png', // SPIDOL PERMANENT ARTLINE MERAH
            'BRG000144' => 'bon-items/spidol.png', // Spidol Kecil Hitam Snowman
            'BRG000145' => 'bon-items/spidol.png', // Spidol Kecil Merah Snowman
            'BRG000146' => 'bon-items/spidol.png', // Spidol Kecil Biru Snowman
            'BRG000148' => 'bon-items/spidol.png', // Spidol Permanen Snowman HITAM
            'BRG000149' => 'bon-items/spidol.png', // Spidol Marker Snowman Merah
            'BRG000150' => 'bon-items/spidol.png', // Spidol Marker Snowman Biru
            'BRG000151' => 'bon-items/spidol.png', // Spidol Board Permanent Marker Biru
            'BRG000152' => 'bon-items/spidol.png', // Spidol Board Permanent Marker Hitam
            'BRG000153' => 'bon-items/spidol.png', // Spidol Board Permanent Marker Merah

            // ── BINDER CLIP & PAPER CLIP ──────────────────────────────────────
            'BRG000085' => 'bon-items/binder_clip.png', // PAPER CLIP NO 01
            'BRG000086' => 'bon-items/binder_clip.png', // PAPER CLIP NO 3
            'BRG000087' => 'bon-items/binder_clip.png', // PAPER CLIP NO 5
            'BRG000090' => 'bon-items/binder_clip.png', // BINDER CLIP 200
            'BRG000097' => 'bon-items/binder_clip.png', // BINDER CLIP 111
            'BRG000116' => 'bon-items/binder_clip.png', // BINDER CLIP 260
            'BRG000137' => 'bon-items/binder_clip.png', // BINDER CLIPS 105
            'BRG000138' => 'bon-items/binder_clip.png', // BINDER CLIPS 107
            'BRG000139' => 'bon-items/binder_clip.png', // BINDER CLIPS 155
            'BRG000224' => 'bon-items/binder_clip.png', // Snal Hackter

            // ── STAPLER & ISI STAPLER ─────────────────────────────────────────
            'BRG000117' => 'bon-items/stapler.png', // ISI STAPLES TEMBAK
            'BRG000118' => 'bon-items/stapler.png', // ISI STAPLER 10-1M
            'BRG000119' => 'bon-items/stapler.png', // ISI STAPLER 3-1M
            'BRG000133' => 'bon-items/stapler.png', // STAPLER KECIL
            'BRG000134' => 'bon-items/stapler.png', // STAPLER BESAR

            // ── TONER / CARTRIDGE ─────────────────────────────────────────────
            'BRG000077' => 'bon-items/toner.png', // Toner HP 215 Hitam Original
            'BRG000078' => 'bon-items/toner.png', // Toner 206 Biru Original
            'BRG000185' => 'bon-items/toner.png', // Toner 505 A
            'BRG000195' => 'bon-items/toner.png', // TONER VALID 3-0200-1
            'BRG000196' => 'bon-items/toner.png', // DRUM TONER CF 232A
            'BRG000197' => 'bon-items/toner.png', // TONER ORIGINAL HP 276A
            'BRG000198' => 'bon-items/toner.png', // TONER COMPATIBLE HP 276A
            'BRG000199' => 'bon-items/toner.png', // Toner HP 215 Biru Original
            'BRG000200' => 'bon-items/toner.png', // Toner HP 215 Kuning Original
            'BRG000201' => 'bon-items/toner.png', // Toner 285
            'BRG000202' => 'bon-items/toner.png', // Toner 230 A
            'BRG000203' => 'bon-items/toner.png', // Toner HP 215 Merah Original
            'BRG000204' => 'bon-items/toner.png', // Toner 206 Kuning Original
            'BRG000205' => 'bon-items/toner.png', // Toner 206 Hitam Original
            'BRG000211' => 'bon-items/toner.png', // Toner 206 Merah Original
            'BRG000217' => 'bon-items/toner.png', // Toner HP 215 Merah Compatible
            'BRG000218' => 'bon-items/toner.png', // Toner HP 215 Kuning Compatible
            'BRG000219' => 'bon-items/toner.png', // Toner HP 215 Biru Compatible
            'BRG000220' => 'bon-items/toner.png', // Toner 206 Hitam Compatible
            'BRG000221' => 'bon-items/toner.png', // Toner 206 Merah Compatible
            'BRG000222' => 'bon-items/toner.png', // Toner 206 Kuning Compatible
            'BRG000223' => 'bon-items/toner.png', // Toner 206 Biru Compatible

            // ── ISOLASI / TAPE / LAKBAN ───────────────────────────────────────
            'BRG000135' => 'bon-items/isolasi_tape.png', // ISOLASI BENING KECIL
            'BRG000136' => 'bon-items/isolasi_tape.png', // ISOLASI BENING BESAR
            'BRG000156' => 'bon-items/isolasi_tape.png', // TAPE DISPENSER
            'BRG000169' => 'bon-items/isolasi_tape.png', // LAKBAN HITAM
            'BRG000170' => 'bon-items/isolasi_tape.png', // LAKBAN BENING
            'BRG000173' => 'bon-items/isolasi_tape.png', // DOUBLE TAPE TEBAL 3M
            'BRG000187' => 'bon-items/isolasi_tape.png', // DOUBLE TAPE 48 MM
            'BRG000225' => 'bon-items/isolasi_tape.png', // DOUBLE TAPE 12 MM
            'BRG000226' => 'bon-items/isolasi_tape.png', // DOUBLE FOAM 24 MM

            // ── BUKU / CATATAN ────────────────────────────────────────────────
            'BRG000057' => 'bon-items/buku.png', // BUKU AGENDA KPP
            'BRG000061' => 'bon-items/buku.png', // BUKU KECIL ORANGE KPP MADYA
            'BRG000068' => 'bon-items/buku.png', // PAPPER LINE NOTE
            'BRG000069' => 'bon-items/buku.png', // BUKU AGENDA (TAS COKLAT)
            'BRG000126' => 'bon-items/buku.png', // BUKU KWITANSI KECIL
            'BRG000127' => 'bon-items/buku.png', // BUKU KWITANSI BESAR
            'BRG000128' => 'bon-items/buku.png', // BUKU TULIS BESAR
            'BRG000129' => 'bon-items/buku.png', // BUKU TULIS PANJANG ISI 200
            'BRG000130' => 'bon-items/buku.png', // BUKU TULIS PANJANG ISI 100
            'BRG000131' => 'bon-items/buku.png', // BUKU TULIS ISI 200
            'BRG000132' => 'bon-items/buku.png', // BUKU TULIS ISI 100
            'BRG000162' => 'bon-items/buku.png', // Kertas Label 104
            'BRG000163' => 'bon-items/buku.png', // Kertas Label 121

            // ── MAP / ORDNER / FILE ───────────────────────────────────────────
            'BRG000079' => 'bon-items/map_ordner.png', // MAP MERAH FUNGSIONAL
            'BRG000080' => 'bon-items/map_ordner.png', // MAP PINK FUNGSIONAL
            'BRG000081' => 'bon-items/map_ordner.png', // MAP PUTIH KANTOR
            'BRG000092' => 'bon-items/map_ordner.png', // PEMBATAS BUKU
            'BRG000093' => 'bon-items/map_ordner.png', // RAK BROSUR
            'BRG000159' => 'bon-items/map_ordner.png', // MIKA BENING JILID (sementara pakai map)
            'BRG000179' => 'bon-items/map_ordner.png', // Bantex Map Clear Holder 1401
            'BRG000216' => 'bon-items/map_ordner.png', // Bantex Box File
            'BRG000227' => 'bon-items/map_ordner.png', // Bantex Map Clear Holder 1465
            'BRG000232' => 'bon-items/map_ordner.png', // Map Kancing

            // ── STICKY NOTE ───────────────────────────────────────────────────
            'BRG000180' => 'bon-items/sticky_note.png', // Sticky Notes 76x51 mm 5 Warna
            'BRG000181' => 'bon-items/sticky_note.png', // Sticky Notes 76x76 mm
            'BRG000182' => 'bon-items/sticky_note.png', // Sign Here Joyko IM42 Plastik
            'BRG000183' => 'bon-items/sticky_note.png', // E-print Kertas Thermal 216x30
            'BRG000184' => 'bon-items/sticky_note.png', // STICKY NOTES 5 WARNA

            // ── GUNTING & CUTTER ──────────────────────────────────────────────
            'BRG000188' => 'bon-items/gunting_cutter.png', // CUTTER
            'BRG000190' => 'bon-items/gunting_cutter.png', // Gunting
            'BRG000193' => 'bon-items/gunting_cutter.png', // ISI CUTTER
            'BRG000230' => 'bon-items/gunting_cutter.png', // Gunting Kecil

            // ── PENSIL, PENGHAPUS, PENGGARIS ──────────────────────────────────
            'BRG000096' => 'bon-items/pensil_penggaris.png', // RAUTAN BESAR
            'BRG000107' => 'bon-items/pensil_penggaris.png', // PENGGARIS 50 CM
            'BRG000143' => 'bon-items/pensil_penggaris.png', // Remover JOYCO (tipe-x)
            'BRG000147' => 'bon-items/pensil_penggaris.png', // REMOVER MAX (tipe-x)
            'BRG000165' => 'bon-items/pensil_penggaris.png', // TIPE X KERTAS
            'BRG000166' => 'bon-items/pensil_penggaris.png', // TIPE X CAIR
            'BRG000189' => 'bon-items/pensil_penggaris.png', // PENGGARIS 30CM
            'BRG000191' => 'bon-items/pensil_penggaris.png', // PENSIL FABER CASTLE
            'BRG000192' => 'bon-items/pensil_penggaris.png', // PENGHAPUS PENSIL
            'BRG000194' => 'bon-items/pensil_penggaris.png', // RAUTAN KECIL
            'BRG000231' => 'bon-items/pensil_penggaris.png', // Rak Pensil

            // ── STEMPEL & BANTALAN ────────────────────────────────────────────
            'BRG000084' => 'bon-items/stempel.png', // BANTALAN STEMPEL PAD 1
            'BRG000091' => 'bon-items/stempel.png', // GANTUNGAN STEMPEL
            'BRG000120' => 'bon-items/stempel.png', // Tinta Trodat - Hitam
            'BRG000121' => 'bon-items/stempel.png', // Tinta Trodat - Biru
            'BRG000122' => 'bon-items/stempel.png', // Tinta Trodat - Violet
            'BRG000123' => 'bon-items/stempel.png', // Bantalan Stempel PAD 0
            'BRG000124' => 'bon-items/stempel.png', // STAMPINK INK
            'BRG000125' => 'bon-items/stempel.png', // Date Stamp

            // ── BATERAI ───────────────────────────────────────────────────────
            'BRG000208' => 'bon-items/baterai.png', // Baterai Besar D Size (R20)
            'BRG000209' => 'bon-items/baterai.png', // Baterai A23
            'BRG000210' => 'bon-items/baterai.png', // Baterai CR 2032
            'BRG000212' => 'bon-items/baterai.png', // Baterai Kotak 9V
            'BRG000213' => 'bon-items/baterai.png', // Baterai AAA 4B+2 Alkaline
            'BRG000214' => 'bon-items/baterai.png', // Baterai AA 4B+2 Alkaline
            'BRG000215' => 'bon-items/baterai.png', // ABC Tipe C (R14)

            // ── CD / MEDIA ────────────────────────────────────────────────────
            'BRG000206' => 'bon-items/cd_media.png', // CD-RW GT-PRO
            'BRG000207' => 'bon-items/cd_media.png', // CD Case

            // ── MISC / ALAT TULIS LAIN ────────────────────────────────────────
            'BRG000085' => 'bon-items/binder_clip.png', // PAPER CLIP NO 01 (duplicate key fix below)
            'BRG000088' => 'bon-items/gunting_cutter.png', // PUNCH NO 85 (PLONG BESAR)
            'BRG000089' => 'bon-items/gunting_cutter.png', // PUNCH NO 30 (PLONG KECIL)
            'BRG000094' => 'bon-items/buku.png',           // DESK SET → buku/alat tulis
            'BRG000095' => 'bon-items/buku.png',           // CLIP BOARD
            'BRG000160' => 'bon-items/buku.png',           // LEM STICK → misc
            'BRG000161' => 'bon-items/buku.png',           // LEM CAIR → misc
            'BRG000164' => 'bon-items/buku.png',           // Kertas Thermal
            'BRG000167' => 'bon-items/binder_clip.png',    // PINES BESI → binder misc
        ];

        // Souvenir items (gambar belum ada, skip dulu - akan diisi setelah kuota reset)
        $souvenirKodes = [
            'BRG000058','BRG000059','BRG000060','BRG000062','BRG000063',
            'BRG000064','BRG000065','BRG000066','BRG000067','BRG000070',
            'BRG000071','BRG000072','BRG000073','BRG000074','BRG000075',
            'BRG000076',
        ];

        $updated = 0;
        $skipped = 0;

        foreach ($mapping as $kode => $imagePath) {
            $rows = DB::table('bon_items')
                ->where('kode_barang', $kode)
                ->update(['image_path' => $imagePath, 'updated_at' => now()]);

            if ($rows > 0) {
                $updated++;
            } else {
                $skipped++;
                $this->command->warn("Kode tidak ditemukan: {$kode}");
            }
        }

        $this->command->info("Update selesai: {$updated} barang di-assign gambar, {$skipped} tidak ditemukan.");
        $this->command->info('Souvenir (' . count($souvenirKodes) . ' barang) belum di-assign - menunggu gambar souvenir, lem, dan mika.');
        $this->command->info('=== Assign gambar selesai! ===');
    }
}
