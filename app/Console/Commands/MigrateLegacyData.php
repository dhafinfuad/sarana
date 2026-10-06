<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Vehicle;

class MigrateLegacyData extends Command
{
    protected $signature = 'data:migrate-legacy';
    protected $description = 'Migrate legacy SQL data from JSON (mobil.json and lisan_out.json) to bookings and permisis tables';

    public function handle()
    {
        $this->info('Starting legacy data migration...');

        // 1. Migrate Bookings
        $mobilJsonPath = base_path('mobil.json');
        if (file_exists($mobilJsonPath)) {
            $this->info('Migrating bookings from mobil.json...');
            $mobilData = json_decode(file_get_contents($mobilJsonPath), true);
            $this->migrateBookings($mobilData);
            $this->info('Bookings migration completed.');
        } else {
            $this->warn('mobil.json not found, skipping bookings migration.');
        }

        // 2. Migrate Permisis
        $lisanJsonPath = base_path('lisan_out.json');
        if (file_exists($lisanJsonPath)) {
            $this->info('Migrating permisis from lisan_out.json...');
            $lisanData = json_decode(file_get_contents($lisanJsonPath), true);
            $this->migratePermisis($lisanData);
            $this->info('Permisis migration completed.');
        } else {
            $this->warn('lisan_out.json not found, skipping permisis migration.');
        }

        $this->info('Migration process finished.');
    }

    private function migrateBookings(array $data)
    {
        $adminUserId = User::orderBy('id')->value('id') ?? 1;
        $defaultVehicleId = Vehicle::orderBy('id')->value('id') ?? 1;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('bookings')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $count = 0;
        foreach ($data as $row) {
            $pegawai = $row['pegawai'] ?? '';
            $user = null;
            if ($pegawai) {
                $user = User::where('name', 'like', "%{$pegawai}%")->first();
            }
            $userId = $user ? $user->id : $adminUserId;

            $platLengkap = $row['plat'] ?? '';
            // Ekstrak plat (misal: "N 1574 AP - All New Ertiga A/T" -> "N 1574 AP")
            $platParts = explode('-', $platLengkap);
            $plat = trim($platParts[0]);

            $vehicle = null;
            if ($plat) {
                $vehicle = Vehicle::where('plat_nomor', 'like', "%{$plat}%")->first();
            }
            $vehicleId = $vehicle ? $vehicle->id : $defaultVehicleId;

            // Penyesuaian sesuai instruksi: default ke Kota Malang
            $kota = "Kota Malang";
            $provinsi = "Jawa Timur";

            // Mapping Status
            $status = 'pending';
            $pengembalian = $row['pengembalian'] ?? '';
            $persetujuan = $row['persetujuan'] ?? '';

            if (strtolower($pengembalian) === 'sudah') {
                $status = 'selesai';
            } elseif (strtolower($persetujuan) === 'setuju') {
                $status = 'disetujui';
            } elseif (strtolower($persetujuan) === 'ditolak') {
                $status = 'ditolak';
            }

            DB::table('bookings')->insert([
                'user_id' => $userId,
                'vehicle_id' => $vehicleId,
                'provinsi' => $provinsi,
                'kota' => $kota,
                'keperluan' => $row['keperluan'] ?? '-',
                'tanggal_mulai' => $row['mulai'] ?? now()->toDateString(),
                'tanggal_selesai' => $row['selesai'] ?? now()->toDateString(),
                'status' => $status,
                'catatan_admin' => $user ? null : "Dibuat untuk pegawai lama: {$pegawai}",
                'processed_by' => in_array($status, ['disetujui', 'ditolak', 'selesai']) ? $adminUserId : null,
                'processed_at' => in_array($status, ['disetujui', 'ditolak', 'selesai']) ? now() : null,
                'created_at' => $row['mulai'] ?? now(),
                'updated_at' => $row['mulai'] ?? now(),
            ]);

            $count++;
        }

        $this->line("Inserted {$count} records into bookings.");
    }

    private function migratePermisis(array $data)
    {
        $adminUserId = User::orderBy('id')->value('id') ?? 1;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permisis')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $count = 0;
        foreach ($data as $row) {
            $nipPendek = $row['nip_pendek'] ?? '';
            $user = null;
            if ($nipPendek) {
                $user = User::where('nip_pendek', $nipPendek)->first();
            }
            $userId = $user ? $user->id : $adminUserId;

            $alasan = $row['alasan'] ?? '-';
            $kategori = $row['kategori'] ?? '';

            // Penyesuaian sesuai instruksi: Auto-fill kategori berdasarkan alasan
            if (empty(trim($kategori))) {
                $alasanLower = strtolower($alasan);
                if (preg_match('/(sakit|berobat|dokter|rs|kontrol|puskesmas|klinik)/', $alasanLower)) {
                    $kategori = 'Kesehatan';
                } elseif (preg_match('/(keluarga|anak|istri|suami|orang tua|takziyah|melayat)/', $alasanLower)) {
                    $kategori = 'Keluarga';
                } elseif (preg_match('/(dinas|rapat|kpp|kanwil)/', $alasanLower)) {
                    $kategori = 'Dinas';
                } else {
                    $kategori = 'Lain-lain';
                }
            }

            // Mapping Status
            $status = 'pending';
            $persetujuan = $row['persetujuan'] ?? '';

            if (strtolower($persetujuan) === 'setuju') {
                $status = 'disetujui';
            } elseif (strtolower($persetujuan) === 'ditolak') {
                $status = 'ditolak';
            }

            // Fallback jenis
            $jenis = $row['permohonan'] ?? 'IZIN';
            if (empty(trim($jenis))) {
                $jenis = 'IZIN';
            }

            DB::table('permisis')->insert([
                'user_id' => $userId,
                'kategori' => $kategori,
                'jenis' => $jenis,
                'keperluan' => $alasan,
                'tanggal_mulai' => $row['mulai'] ?? now(),
                'tanggal_selesai' => $row['selesai'] ?? now(),
                'status' => $status,
                'created_at' => $row['mulai'] ?? now(),
                'updated_at' => $row['mulai'] ?? now(),
            ]);

            $count++;
        }

        $this->line("Inserted {$count} records into permisis.");
    }
}
