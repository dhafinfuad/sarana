<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\BonItem;

class BonItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['kode_barang' => 'ATK000001', 'nama_barang' => 'Barang A', 'stok' => 23],
            ['kode_barang' => 'ATK000002', 'nama_barang' => 'Barang B', 'stok' => 53],
            ['kode_barang' => 'ATK000003', 'nama_barang' => 'Tes Barang', 'stok' => 4],
            ['kode_barang' => 'ATK000004', 'nama_barang' => 'Barang Ag', 'stok' => 55],
            ['kode_barang' => 'ATK000005', 'nama_barang' => 'barang a', 'stok' => 8],
        ];

        foreach ($items as $item) {
            BonItem::firstOrCreate(
                ['kode_barang' => $item['kode_barang']],
                [
                    'nama_barang' => $item['nama_barang'],
                    'satuan_id' => 1, // Default to PCS
                    'stok' => $item['stok'],
                    'stok_minimum' => 5,
                    'created_by' => \App\Models\User::first()->id ?? 1,
                ]
            );
        }
    }
}
