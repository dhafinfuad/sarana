<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AsmaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseDir = base_path('asmara');
        $files = ['jenis_surat.sql', 'jenis_pj.sql', 'tbl_nomor_awal.sql', 'surat_keluar.sql'];

        $this->command->info('Memulai import data Asmara (membaca file SQL asli)...');
        
        DB::unprepared("SET NAMES utf8mb4;");
        DB::unprepared("SET FOREIGN_KEY_CHECKS = 0;");
        
        // Bersihkan tabel dulu agar tidak duplikat jika dijalankan ulang
        DB::unprepared("TRUNCATE TABLE `jenis_surat`;");
        DB::unprepared("TRUNCATE TABLE `jenis_pj`;");
        DB::unprepared("TRUNCATE TABLE `tbl_nomor_awal`;");
        DB::unprepared("TRUNCATE TABLE `surat_keluar`;");
        $this->command->info('Tabel dibersihkan (TRUNCATE).');

        foreach ($files as $file) {
            $path = $baseDir . '/' . $file;
            if (!File::exists($path)) {
                $this->command->warn("File {$path} tidak ditemukan, dilewati.");
                continue;
            }
            
            $this->command->info("Memproses {$file}...");
            $handle = fopen($path, "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $line = trim($line);
                    if (str_starts_with($line, "INSERT INTO")) {
                        // Ubah INSERT INTO menjadi INSERT IGNORE INTO untuk toleransi duplikat
                        $line = str_ireplace('INSERT INTO `jenis_surat` VALUES', 'INSERT IGNORE INTO `jenis_surat` VALUES', $line);
                        $line = str_ireplace('INSERT INTO `jenis_pj` VALUES', 'INSERT IGNORE INTO `jenis_pj` VALUES', $line);
                        $line = str_ireplace('INSERT INTO `surat_keluar` VALUES', 'INSERT IGNORE INTO `surat_keluar` VALUES', $line);

                        // Perbaiki column count untuk tbl_nomor_awal karena kita menambahkan id() di laravel
                        if ($file === 'tbl_nomor_awal.sql') {
                            $line = str_ireplace('INSERT INTO `tbl_nomor_awal` VALUES', 'INSERT IGNORE INTO `tbl_nomor_awal` (`jenis_surat`, `jenis_pj`, `tahun`, `nomor_awal`) VALUES', $line);
                        }
                        
                        DB::unprepared($line);
                    }
                }
                fclose($handle);
            }
            $this->command->info("  ✓ {$file} selesai.");
        }

        DB::unprepared("SET FOREIGN_KEY_CHECKS = 1;");
        $this->command->info('Data Asmara berhasil diimport!');
    }
}
