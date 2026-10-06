<?php

namespace App\Imports\Bona;

use App\Models\BonItem;
use App\Models\BonSatuan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MasterBarangImport implements ToCollection, WithHeadingRow
{
    private int $userId;

    public function __construct(?int $userId = null)
    {
        $this->userId = $userId ?? (int) (Auth::user()?->id ?? \App\Models\User::first()?->id ?? 1);
    }

    public function collection(Collection $rows)
    {
        // Hitung nomor urut angka tertinggi dari kode barang yang sudah ada
        $maxNum = 0;
        $existingItems = BonItem::select('kode_barang')->get();
        foreach ($existingItems as $existing) {
            if (preg_match('/(\d+)/', $existing->kode_barang, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }
        $lastNum = $maxNum;

        foreach ($rows as $row) {
            // Check if required columns exist in the row
            if (!isset($row['nama_barang'])) {
                continue; // Skip if no nama_barang
            }

            $namaBarang = trim((string)$row['nama_barang']);
            if (empty($namaBarang)) {
                continue;
            }

            // Kode Barang dari file Excel (opsional)
            $kodeBarangInput = isset($row['kode_barang']) ? trim((string)$row['kode_barang']) : '';

            // Handle Satuan
            $namaSatuan = isset($row['satuan']) ? strtoupper(trim((string)$row['satuan'])) : 'PCS';
            if (empty($namaSatuan)) {
                $namaSatuan = 'PCS';
            }

            $satuan = BonSatuan::firstOrCreate(
                ['nama_satuan' => $namaSatuan],
                ['nama_satuan' => $namaSatuan]
            );

            $stokSaatIni = isset($row['stok_saat_ini']) && is_numeric($row['stok_saat_ini']) ? (int) $row['stok_saat_ini'] : 0;
            $stokMinimum = isset($row['stok_minimum']) && is_numeric($row['stok_minimum']) ? (int) $row['stok_minimum'] : 0;

            // Pencarian data yang sudah ada:
            // 1. Jika kode_barang diisi di Excel, cari berdasarkan kode_barang terlebih dahulu
            $item = null;
            if (!empty($kodeBarangInput)) {
                $item = BonItem::where('kode_barang', $kodeBarangInput)->first();
            }

            // 2. Jika tidak ditemukan berdasarkan kode_barang (atau kode_barang kosong), cari berdasarkan nama_barang
            if (!$item) {
                $item = BonItem::where('nama_barang', $namaBarang)->first();
            }

            if ($item) {
                // Update item yang sudah ada
                $updateData = [
                    'nama_barang' => $namaBarang,
                    'satuan_id' => $satuan->id,
                    'stok' => $stokSaatIni,
                    'stok_minimum' => $stokMinimum,
                ];

                // Jika user memberikan kode_barang baru dan belum dipakai item lain, perbarui kode_barang
                if (!empty($kodeBarangInput) && $item->kode_barang !== $kodeBarangInput) {
                    $conflict = BonItem::where('kode_barang', $kodeBarangInput)->where('id', '!=', $item->id)->exists();
                    if (!$conflict) {
                        $updateData['kode_barang'] = $kodeBarangInput;
                    }
                }

                $item->update($updateData);
            } else {
                // Buat item baru
                if (!empty($kodeBarangInput)) {
                    // Gunakan kode barang dari input jika belum dipakai
                    $conflict = BonItem::where('kode_barang', $kodeBarangInput)->exists();
                    if (!$conflict) {
                        $kodeBarang = $kodeBarangInput;
                    } else {
                        // Jika bentrok, buat kode otomatis
                        do {
                            $lastNum++;
                            $kodeBarang = 'ATK' . str_pad($lastNum, 6, '0', STR_PAD_LEFT);
                        } while (BonItem::where('kode_barang', $kodeBarang)->exists());
                    }
                } else {
                    // Auto-generate kode barang jika dikosongkan
                    do {
                        $lastNum++;
                        $kodeBarang = 'ATK' . str_pad($lastNum, 6, '0', STR_PAD_LEFT);
                    } while (BonItem::where('kode_barang', $kodeBarang)->exists());
                }

                BonItem::create([
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => $namaBarang,
                    'satuan_id' => $satuan->id,
                    'stok' => $stokSaatIni,
                    'stok_minimum' => $stokMinimum,
                    'created_by' => $this->userId,
                ]);
            }
        }
    }
}
