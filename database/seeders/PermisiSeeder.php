<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Permisi;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class PermisiSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = base_path('Data Awal Permisi.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error('File Data Awal Permisi.xlsx tidak ditemukan di root proyek.');
            return;
        }

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $highestRow = $worksheet->getHighestRow();
        
        $this->command->info('Memulai import data Permisi...');

        $count = 0;

        for ($row = 2; $row <= $highestRow; $row++) {
            $nipPendek = (string) $worksheet->getCell("C{$row}")->getValue();
            if (empty($nipPendek)) continue;

            $nama = $worksheet->getCell("B{$row}")->getValue();
            $seksi = $worksheet->getCell("D{$row}")->getValue();
            $mulai = $worksheet->getCell("E{$row}")->getValue();
            $selesai = $worksheet->getCell("F{$row}")->getValue();
            $kategori = $worksheet->getCell("G{$row}")->getValue();
            $jenis = $worksheet->getCell("H{$row}")->getValue();
            $keperluan = $worksheet->getCell("I{$row}")->getValue();
            $statusExcel = strtolower(trim((string) $worksheet->getCell("J{$row}")->getValue()));

            // Find or create user
            $user = User::firstOrCreate(
                ['nip_pendek' => $nipPendek],
                [
                    'name' => $nama,
                    'nip' => str_pad($nipPendek, 18, '0', STR_PAD_RIGHT), // Dummy NIP panjang
                    'jabatan' => 'Pelaksana',
                    'seksi' => $seksi,
                    'password' => Hash::make($nipPendek)
                ]
            );

            // Parse dates. Handle numeric excel dates or string.
            if (is_numeric($mulai)) {
                $tanggalMulai = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mulai));
            } else {
                $tanggalMulai = Carbon::parse($mulai);
            }

            if (is_numeric($selesai)) {
                $tanggalSelesai = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($selesai));
            } else {
                $tanggalSelesai = Carbon::parse($selesai);
            }

            // Map status
            $status = match($statusExcel) {
                'disetujui' => 'disetujui',
                'ditolak' => 'ditolak',
                'selesai' => 'selesai',
                'dibatalkan' => 'dibatalkan',
                default => 'pending'
            };

            Permisi::create([
                'user_id' => $user->id,
                'kategori' => $kategori ?: 'Lain-lain',
                'jenis' => $jenis ?: 'Permohonan',
                'keperluan' => $keperluan,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'status' => $status
            ]);

            $count++;
        }

        $this->command->info("Selesai import {$count} data perizinan.");
    }
}
