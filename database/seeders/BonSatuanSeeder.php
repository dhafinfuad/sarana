<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\BonSatuan;

class BonSatuanSeeder extends Seeder
{
    public function run(): void
    {
        $satuans = ['PCS', 'BOX', 'RIM', 'LUSIN', 'PACK'];

        foreach ($satuans as $satuan) {
            BonSatuan::firstOrCreate(['nama_satuan' => $satuan]);
        }
    }
}
