<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportBonItemsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Import Data Barang dari db_pembelian ===');

        // ─── 1. TRUNCATE (disable FK checks first) ────────────────────────────
        $this->command->info('Truncating bon_items, bon_satuans...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('bon_request_items')->truncate();
        DB::table('bon_requests')->truncate();
        DB::table('bon_items')->truncate();
        DB::table('bon_satuans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Truncate selesai.');

        // ─── 2. SATUAN ────────────────────────────────────────────────────────
        $this->command->info('Mengisi tabel bon_satuans...');
        DB::table('bon_satuans')->insert([
            ['nama_satuan' => 'PCS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'BOX', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $idPCS = DB::table('bon_satuans')->where('nama_satuan', 'PCS')->value('id');
        $idBOX = DB::table('bon_satuans')->where('nama_satuan', 'BOX')->value('id');
        $this->command->info("Satuan PCS id={$idPCS}, BOX id={$idBOX}");

        // ─── 3. Ambil user pertama sebagai created_by ──────────────────────────
        $adminId = DB::table('users')->orderBy('id')->value('id');
        if (!$adminId) {
            $this->command->error('Tidak ada user di tabel users! Seeder dihentikan.');
            return;
        }
        $this->command->info("Menggunakan user id={$adminId} sebagai created_by.");

        // ─── 4. DATA BARANG ────────────────────────────────────────────────────
        // Skip BRG000001–BRG000056 (placeholder 'X')
        // Trim nama, semua satuan = PCS (default)
        $rawData = [
            ['kode' => 'BRG000057', 'nama' => 'BUKU AGENDA KPP', 'stok' => 314],
            ['kode' => 'BRG000058', 'nama' => 'SAPU TANGAN KECIL', 'stok' => 93],
            ['kode' => 'BRG000059', 'nama' => 'TUMBLER SET', 'stok' => 24],
            ['kode' => 'BRG000060', 'nama' => 'POUCH KECIL', 'stok' => 22],
            ['kode' => 'BRG000061', 'nama' => 'BUKU KECIL ORANGE KPP MADYA', 'stok' => 294],
            ['kode' => 'BRG000062', 'nama' => 'SEDOTAN STAINLESS', 'stok' => 12],
            ['kode' => 'BRG000063', 'nama' => 'HEADSET', 'stok' => 1],
            ['kode' => 'BRG000064', 'nama' => 'TUMBLER KECIL', 'stok' => 8],
            ['kode' => 'BRG000065', 'nama' => 'TEMPAT MAKAN BIASA', 'stok' => 6],
            ['kode' => 'BRG000066', 'nama' => 'PAYUNG TANGGUNG', 'stok' => 2],
            ['kode' => 'BRG000067', 'nama' => 'TEMPAT MAKAN 651', 'stok' => 2],
            ['kode' => 'BRG000068', 'nama' => 'PAPPER LINE NOTE', 'stok' => 139],
            ['kode' => 'BRG000069', 'nama' => 'BUKU AGENDA (TAS COKLAT)', 'stok' => 6],
            ['kode' => 'BRG000070', 'nama' => 'TUMBLER PINK', 'stok' => 23],
            ['kode' => 'BRG000071', 'nama' => 'CLUTCH (TEMPAT IPAD)', 'stok' => 2],
            ['kode' => 'BRG000072', 'nama' => 'POUCH BESAR', 'stok' => 34],
            ['kode' => 'BRG000073', 'nama' => 'CANGKIR', 'stok' => 8],
            ['kode' => 'BRG000074', 'nama' => 'SYAL BATIK', 'stok' => 21],
            ['kode' => 'BRG000075', 'nama' => 'PAKET BOLPOINT + PENSIL + TIP X', 'stok' => 350],
            ['kode' => 'BRG000076', 'nama' => 'BANTAL LEHER', 'stok' => 11],
            ['kode' => 'BRG000077', 'nama' => 'Toner HP 215 Hitam Original', 'stok' => 3],
            ['kode' => 'BRG000078', 'nama' => 'Toner 206 Biru Original', 'stok' => 3],
            ['kode' => 'BRG000079', 'nama' => 'MAP MERAH FUNGSIONAL', 'stok' => 3079],
            ['kode' => 'BRG000080', 'nama' => 'MAP PINK FUNGSIONAL', 'stok' => 3675],
            ['kode' => 'BRG000081', 'nama' => 'MAP PUTIH KANTOR', 'stok' => 5100],
            ['kode' => 'BRG000082', 'nama' => 'SPIDOL NON PERMANENT ARTLINE BIRU', 'stok' => 8],
            ['kode' => 'BRG000083', 'nama' => 'SPIDOL PERMANENT ARTLINE MERAH', 'stok' => 24],
            ['kode' => 'BRG000084', 'nama' => 'BANTALAN STEMPEL PAD 1', 'stok' => 8],
            ['kode' => 'BRG000085', 'nama' => 'PAPER CLIP NO 01', 'stok' => 318],
            ['kode' => 'BRG000086', 'nama' => 'PAPER CLIP NO 3', 'stok' => 395],
            ['kode' => 'BRG000087', 'nama' => 'PAPER CLIP NO 5', 'stok' => 226],
            ['kode' => 'BRG000088', 'nama' => 'PUNCH NO 85 (PLONG BESAR)', 'stok' => 16],
            ['kode' => 'BRG000089', 'nama' => 'PUNCH NO 30 (PLONG KECIL)', 'stok' => 38],
            ['kode' => 'BRG000090', 'nama' => 'BINDER CLIP 200', 'stok' => 168],
            ['kode' => 'BRG000091', 'nama' => 'GANTUNGAN STEMPEL', 'stok' => 5],
            ['kode' => 'BRG000092', 'nama' => 'PEMBATAS BUKU', 'stok' => 59],
            ['kode' => 'BRG000093', 'nama' => 'RAK BROSUR', 'stok' => 2],
            ['kode' => 'BRG000094', 'nama' => 'DESK SET', 'stok' => 9],
            ['kode' => 'BRG000095', 'nama' => 'CLIP BOARD (ALAS NULIS)', 'stok' => 115],
            ['kode' => 'BRG000096', 'nama' => 'RAUTAN BESAR', 'stok' => 14],
            ['kode' => 'BRG000097', 'nama' => 'BINDER CLIP 111', 'stok' => 293],
            ['kode' => 'BRG000098', 'nama' => 'BOLPOINT BAOKE 1.0 MM', 'stok' => 82],
            ['kode' => 'BRG000099', 'nama' => 'BOLPOINT SNOWMAN MERAH', 'stok' => 34],
            ['kode' => 'BRG000100', 'nama' => 'BOLPOINT BOLDLINER HITAM', 'stok' => 186],
            ['kode' => 'BRG000101', 'nama' => 'BOLPOINT BOLDLINER BLUE', 'stok' => 169],
            ['kode' => 'BRG000102', 'nama' => 'BOLPOINT BIRU PICK KNOCK', 'stok' => 50],
            ['kode' => 'BRG000103', 'nama' => 'BOLPOINT BIRU GEL MERK ZEBRA SARASA', 'stok' => 18],
            ['kode' => 'BRG000104', 'nama' => 'BOLPOINT HITAM PIC KNOCK', 'stok' => 401],
            ['kode' => 'BRG000105', 'nama' => 'BOLPOINT HITAM JOYCO-GP 279', 'stok' => 135],
            ['kode' => 'BRG000106', 'nama' => 'BOLPOINT HITAM KOKORO SWEET', 'stok' => 156],
            ['kode' => 'BRG000107', 'nama' => 'PENGGARIS 50 CM', 'stok' => 49],
            ['kode' => 'BRG000108', 'nama' => 'STABILO BIRU', 'stok' => 1],
            ['kode' => 'BRG000109', 'nama' => 'STABILO UNGU', 'stok' => 4],
            ['kode' => 'BRG000110', 'nama' => 'STABILO KUNING (1)', 'stok' => 0],
            ['kode' => 'BRG000111', 'nama' => 'STABILO ABU ABU', 'stok' => 4],
            ['kode' => 'BRG000112', 'nama' => 'STABILO ORANGE', 'stok' => 7],
            ['kode' => 'BRG000113', 'nama' => 'STABILO PINK', 'stok' => 4],
            ['kode' => 'BRG000114', 'nama' => 'STABILO KUNING (2)', 'stok' => 12],
            ['kode' => 'BRG000115', 'nama' => 'STABILO HIJAU', 'stok' => 22],
            ['kode' => 'BRG000116', 'nama' => 'BINDER CLIP 260', 'stok' => 83],
            ['kode' => 'BRG000117', 'nama' => 'ISI STAPLES TEMBAK', 'stok' => 8],
            ['kode' => 'BRG000118', 'nama' => 'ISI STAPLER 10-1M', 'stok' => 2131],
            ['kode' => 'BRG000119', 'nama' => 'ISI STAPLER 3-1M', 'stok' => 667],
            ['kode' => 'BRG000120', 'nama' => 'Tinta Trodat - Hitam', 'stok' => 12],
            ['kode' => 'BRG000121', 'nama' => 'Tinta Trodat - Biru', 'stok' => 27],
            ['kode' => 'BRG000122', 'nama' => 'Tinta Trodat - Violet', 'stok' => 42],
            ['kode' => 'BRG000123', 'nama' => 'Bantalan Stempel PAD 0', 'stok' => 11],
            ['kode' => 'BRG000124', 'nama' => 'STAMPINK INK (TINTA BANTALAN STEMPEL)', 'stok' => 12],
            ['kode' => 'BRG000125', 'nama' => 'Date Stamp', 'stok' => 22],
            ['kode' => 'BRG000126', 'nama' => 'BUKU KWITANSI KECIL', 'stok' => 19],
            ['kode' => 'BRG000127', 'nama' => 'BUKU KWITANSI BESAR', 'stok' => 26],
            ['kode' => 'BRG000128', 'nama' => 'BUKU TULIS BESAR', 'stok' => 65],
            ['kode' => 'BRG000129', 'nama' => 'BUKU TULIS PANJANG ISI 200', 'stok' => 62],
            ['kode' => 'BRG000130', 'nama' => 'BUKU TULIS PANJANG ISI 100', 'stok' => 29],
            ['kode' => 'BRG000131', 'nama' => 'BUKU TULIS ISI 200', 'stok' => 9],
            ['kode' => 'BRG000132', 'nama' => 'BUKU TULIS ISI 100', 'stok' => 59],
            ['kode' => 'BRG000133', 'nama' => 'STAPLER KECIL', 'stok' => 130],
            ['kode' => 'BRG000134', 'nama' => 'STAPLER BESAR', 'stok' => 104],
            ['kode' => 'BRG000135', 'nama' => 'ISOLASI BENING KECIL', 'stok' => 281],
            ['kode' => 'BRG000136', 'nama' => 'ISOLASI BENING BESAR', 'stok' => 35],
            ['kode' => 'BRG000137', 'nama' => 'BINDER CLIPS 105', 'stok' => 776],
            ['kode' => 'BRG000138', 'nama' => 'BINDER CLIPS 107', 'stok' => 407],
            ['kode' => 'BRG000139', 'nama' => 'BINDER CLIPS 155', 'stok' => 434],
            ['kode' => 'BRG000140', 'nama' => 'Balliner Pilot Merah', 'stok' => 90],
            ['kode' => 'BRG000141', 'nama' => 'BALLINER PILOT BIRU', 'stok' => 132],
            ['kode' => 'BRG000142', 'nama' => 'BALLINER PILOT HITAM', 'stok' => 324],
            ['kode' => 'BRG000143', 'nama' => 'Remover JOYCO', 'stok' => 117],
            ['kode' => 'BRG000144', 'nama' => 'Spidol Kecil Hitam Snowman', 'stok' => 99],
            ['kode' => 'BRG000145', 'nama' => 'Spidol Kecil Merah Snowman', 'stok' => 106],
            ['kode' => 'BRG000146', 'nama' => 'Spidol Kecil Biru Snowman', 'stok' => 122],
            ['kode' => 'BRG000147', 'nama' => 'REMOVER MAX', 'stok' => 9],
            ['kode' => 'BRG000148', 'nama' => 'Spidol Permanen Snowman - HITAM', 'stok' => 140],
            ['kode' => 'BRG000149', 'nama' => 'Spidol Marker Snowman - Merah', 'stok' => 77],
            ['kode' => 'BRG000150', 'nama' => 'Spidol Marker Snowman - Biru', 'stok' => 178],
            ['kode' => 'BRG000151', 'nama' => 'Spidol Board Permanent Marker Biru', 'stok' => 31],
            ['kode' => 'BRG000152', 'nama' => 'Spidol Board Permanent Marker Hitam', 'stok' => 116],
            ['kode' => 'BRG000153', 'nama' => 'Spidol Board Permanent Marker Merah', 'stok' => 33],
            ['kode' => 'BRG000154', 'nama' => 'Kertas Buffalo HVS A4 Merah', 'stok' => 23],
            ['kode' => 'BRG000155', 'nama' => 'Kertas Buffalo HVS A4 Biru', 'stok' => 33],
            ['kode' => 'BRG000156', 'nama' => 'TAPE DISPENSER (ROLL ISOLASI KECIL)', 'stok' => 50],
            ['kode' => 'BRG000157', 'nama' => 'Kertas HVS 70gr A4 Sidu', 'stok' => 301],
            ['kode' => 'BRG000158', 'nama' => 'Kertas HVS 70gr F4 Sidu', 'stok' => 190],
            ['kode' => 'BRG000159', 'nama' => 'MIKA BENING JILID', 'stok' => 16],
            ['kode' => 'BRG000160', 'nama' => 'LEM STICK', 'stok' => 144],
            ['kode' => 'BRG000161', 'nama' => 'LEM CAIR', 'stok' => 9],
            ['kode' => 'BRG000162', 'nama' => 'Kertas Label 104', 'stok' => 98],
            ['kode' => 'BRG000163', 'nama' => 'Kertas Label 121', 'stok' => 105],
            ['kode' => 'BRG000164', 'nama' => 'E-print Kertas Thermal 80x80 73meter (Roll TPT)', 'stok' => 7],
            ['kode' => 'BRG000165', 'nama' => 'TIPE X KERTAS', 'stok' => 59],
            ['kode' => 'BRG000166', 'nama' => 'TIPE X CAIR', 'stok' => 78],
            ['kode' => 'BRG000167', 'nama' => 'PINES BESI', 'stok' => 10],
            ['kode' => 'BRG000168', 'nama' => 'AMPLOP 90 PAPERLINE BESAR', 'stok' => 20],
            ['kode' => 'BRG000169', 'nama' => 'LAKBAN HITAM', 'stok' => 103],
            ['kode' => 'BRG000170', 'nama' => 'LAKBAN BENING', 'stok' => 141],
            ['kode' => 'BRG000171', 'nama' => 'Amplop Dinas F4 Putih', 'stok' => 3000],
            ['kode' => 'BRG000172', 'nama' => 'AMPLOP 110 PAPERLINE KECIL', 'stok' => 14],
            ['kode' => 'BRG000173', 'nama' => 'DOUBLE TAPE TEBAL 3M', 'stok' => 134],
            ['kode' => 'BRG000174', 'nama' => 'Amplop Dinas F4 Coklat', 'stok' => 1700],
            ['kode' => 'BRG000175', 'nama' => 'Amplop Dinas A4 Coklat', 'stok' => 2300],
            ['kode' => 'BRG000176', 'nama' => 'AMPLOP NON KACA KECIL PUTIH', 'stok' => 10200],
            ['kode' => 'BRG000177', 'nama' => 'Paperline Amplop EV-110 PPS (Amplop Putih Kecil)', 'stok' => 16],
            ['kode' => 'BRG000178', 'nama' => 'AMPLOP COKLAT KACA KECIL', 'stok' => 4900],
            ['kode' => 'BRG000179', 'nama' => 'Bantex Map Clear Holder (Ordner) 1401', 'stok' => 205],
            ['kode' => 'BRG000180', 'nama' => 'Sticky Notes 76x51 mm 5 Warna', 'stok' => 153],
            ['kode' => 'BRG000181', 'nama' => 'Sticky Notes 76x76 mm', 'stok' => 339],
            ['kode' => 'BRG000182', 'nama' => 'Sign Here Joyko IM42 Plastik', 'stok' => 302],
            ['kode' => 'BRG000183', 'nama' => 'E-print Kertas Thermal 216x30 (Roll TPT)', 'stok' => 70],
            ['kode' => 'BRG000184', 'nama' => 'STICKY NOTES 5 WARNA (SIGN HERE)', 'stok' => 371],
            ['kode' => 'BRG000185', 'nama' => 'Toner 505 A', 'stok' => 2],
            ['kode' => 'BRG000186', 'nama' => 'AMPLOP COKLAT KACA PANJANG', 'stok' => 4900],
            ['kode' => 'BRG000187', 'nama' => 'DOUBLE TAPE 48 MM', 'stok' => 19],
            ['kode' => 'BRG000188', 'nama' => 'CUTTER', 'stok' => 103],
            ['kode' => 'BRG000189', 'nama' => 'PENGGARIS 30CM', 'stok' => 85],
            ['kode' => 'BRG000190', 'nama' => 'Gunting', 'stok' => 58],
            ['kode' => 'BRG000191', 'nama' => 'PENSIL FABER CASTLE', 'stok' => 356],
            ['kode' => 'BRG000192', 'nama' => 'PENGHAPUS PENSIL', 'stok' => 220],
            ['kode' => 'BRG000193', 'nama' => 'ISI CUTTER', 'stok' => 142],
            ['kode' => 'BRG000194', 'nama' => 'RAUTAN KECIL', 'stok' => 130],
            ['kode' => 'BRG000195', 'nama' => 'TONER VALID 3-0200-1', 'stok' => 4],
            ['kode' => 'BRG000196', 'nama' => 'DRUM TONER CF 232A', 'stok' => 28],
            ['kode' => 'BRG000197', 'nama' => 'TONER ORIGINAL HP 276A', 'stok' => 1],
            ['kode' => 'BRG000198', 'nama' => 'TONER COMPATIBLE HP 276A', 'stok' => 4],
            ['kode' => 'BRG000199', 'nama' => 'Toner HP 215 Biru Original', 'stok' => 3],
            ['kode' => 'BRG000200', 'nama' => 'Toner HP 215 Kuning Original', 'stok' => 3],
            ['kode' => 'BRG000201', 'nama' => 'Toner 285', 'stok' => 37],
            ['kode' => 'BRG000202', 'nama' => 'Toner 230 A', 'stok' => 90],
            ['kode' => 'BRG000203', 'nama' => 'Toner HP 215 Merah Original', 'stok' => 3],
            ['kode' => 'BRG000204', 'nama' => 'Toner 206 Kuning Original', 'stok' => 3],
            ['kode' => 'BRG000205', 'nama' => 'Toner 206 Hitam Original', 'stok' => 2],
            ['kode' => 'BRG000206', 'nama' => 'CD-RW GT-PRO', 'stok' => 97],
            ['kode' => 'BRG000207', 'nama' => 'CD Case', 'stok' => 7],
            ['kode' => 'BRG000208', 'nama' => 'Baterai Besar D Size (R20)', 'stok' => 37],
            ['kode' => 'BRG000209', 'nama' => 'Baterai A23', 'stok' => 35],
            ['kode' => 'BRG000210', 'nama' => 'Baterai CR 2032', 'stok' => 55],
            ['kode' => 'BRG000211', 'nama' => 'Toner 206 Merah Original', 'stok' => 3],
            ['kode' => 'BRG000212', 'nama' => 'Baterai Kotak 9V', 'stok' => 25],
            ['kode' => 'BRG000213', 'nama' => 'Baterai AAA 4B+2 Alkaline', 'stok' => 251],
            ['kode' => 'BRG000214', 'nama' => 'Baterai AA 4B+2 Alkaline', 'stok' => 543],
            ['kode' => 'BRG000215', 'nama' => 'ABC Tipe C (R14)', 'stok' => 38],
            ['kode' => 'BRG000216', 'nama' => 'Bantex Box File', 'stok' => 69],
            ['kode' => 'BRG000217', 'nama' => 'Toner HP 215 Merah Compatible', 'stok' => 6],
            ['kode' => 'BRG000218', 'nama' => 'Toner HP 215 Kuning Compatible', 'stok' => 4],
            ['kode' => 'BRG000219', 'nama' => 'Toner HP 215 Biru Compatible', 'stok' => 5],
            ['kode' => 'BRG000220', 'nama' => 'Toner 206 Hitam Compatible', 'stok' => 1],
            ['kode' => 'BRG000221', 'nama' => 'Toner 206 Merah Compatible', 'stok' => 2],
            ['kode' => 'BRG000222', 'nama' => 'Toner 206 Kuning Compatible', 'stok' => 6],
            ['kode' => 'BRG000223', 'nama' => 'Toner 206 Biru Compatible', 'stok' => 3],
            ['kode' => 'BRG000224', 'nama' => 'Snal Hackter', 'stok' => 250],
            ['kode' => 'BRG000225', 'nama' => 'DOUBLE TAPE 12 MM', 'stok' => 17],
            ['kode' => 'BRG000226', 'nama' => 'DOUBLE FOAM 24 MM', 'stok' => 57],
            ['kode' => 'BRG000227', 'nama' => 'Bantex Map Clear Holder (Ordner) 1465', 'stok' => 18],
            ['kode' => 'BRG000228', 'nama' => 'AMPLOP KACA KECIL PUTIH', 'stok' => 4100],
            ['kode' => 'BRG000229', 'nama' => 'Amplop Dinas A4 Putih', 'stok' => 2100],
            ['kode' => 'BRG000230', 'nama' => 'Gunting Kecil', 'stok' => 0],
            ['kode' => 'BRG000231', 'nama' => 'Rak Pensil', 'stok' => 0],
            ['kode' => 'BRG000232', 'nama' => 'Map Kancing', 'stok' => 100],
        ];

        $now  = now();
        $rows = [];
        foreach ($rawData as $item) {
            $rows[] = [
                'kode_barang'  => $item['kode'],
                'nama_barang'  => trim($item['nama']),
                'satuan_id'    => $idPCS,
                'stok'         => $item['stok'],
                'stok_minimum' => 5,
                'image_path'   => null,
                'created_by'   => $adminId,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('bon_items')->insert($chunk);
        }

        $jumlah = count($rows);
        $this->command->info("Berhasil import {$jumlah} barang ke tabel bon_items.");
        $this->command->info('=== Import selesai! ===');
    }
}
