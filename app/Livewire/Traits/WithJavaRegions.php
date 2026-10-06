<?php

namespace App\Livewire\Traits;

trait WithJavaRegions
{
    public function getJavaRegions(): array
    {
        return [
            'Banten' => [
                'Kota Cilegon', 'Kota Serang', 'Kota Tangerang', 'Kota Tangerang Selatan',
                'Kabupaten Lebak', 'Kabupaten Pandeglang', 'Kabupaten Serang', 'Kabupaten Tangerang'
            ],
            'DKI Jakarta' => [
                'Kota Jakarta Barat', 'Kota Jakarta Pusat', 'Kota Jakarta Selatan',
                'Kota Jakarta Timur', 'Kota Jakarta Utara', 'Kabupaten Kepulauan Seribu'
            ],
            'Jawa Barat' => [
                'Kota Bandung', 'Kota Banjar', 'Kota Bekasi', 'Kota Bogor', 'Kota Cimahi',
                'Kota Cirebon', 'Kota Depok', 'Kota Sukabumi', 'Kota Tasikmalaya',
                'Kabupaten Bandung', 'Kabupaten Bandung Barat', 'Kabupaten Bekasi',
                'Kabupaten Bogor', 'Kabupaten Ciamis', 'Kabupaten Cianjur',
                'Kabupaten Cirebon', 'Kabupaten Garut', 'Kabupaten Indramayu',
                'Kabupaten Karawang', 'Kabupaten Kuningan', 'Kabupaten Majalengka',
                'Kabupaten Pangandaran', 'Kabupaten Purwakarta', 'Kabupaten Subang',
                'Kabupaten Sukabumi', 'Kabupaten Sumedang', 'Kabupaten Tasikmalaya'
            ],
            'Jawa Tengah' => [
                'Kota Magelang', 'Kota Pekalongan', 'Kota Salatiga', 'Kota Semarang',
                'Kota Surakarta', 'Kota Tegal', 'Kabupaten Banjarnegara', 'Kabupaten Banyumas',
                'Kabupaten Batang', 'Kabupaten Blora', 'Kabupaten Boyolali', 'Kabupaten Brebes',
                'Kabupaten Cilacap', 'Kabupaten Demak', 'Kabupaten Grobogan', 'Kabupaten Jepara',
                'Kabupaten Karanganyar', 'Kabupaten Kebumen', 'Kabupaten Kendal', 'Kabupaten Klaten',
                'Kabupaten Kudus', 'Kabupaten Magelang', 'Kabupaten Pati', 'Kabupaten Pekalongan',
                'Kabupaten Pemalang', 'Kabupaten Purbalingga', 'Kabupaten Purworejo', 'Kabupaten Rembang',
                'Kabupaten Semarang', 'Kabupaten Sragen', 'Kabupaten Sukoharjo', 'Kabupaten Tegal',
                'Kabupaten Temanggung', 'Kabupaten Wonogiri', 'Kabupaten Wonosobo'
            ],
            'DI Yogyakarta' => [
                'Kota Yogyakarta', 'Kabupaten Bantul', 'Kabupaten Gunungkidul',
                'Kabupaten Kulon Progo', 'Kabupaten Sleman'
            ],
            'Jawa Timur' => [
                'Kota Batu', 'Kota Blitar', 'Kota Kediri', 'Kota Madiun', 'Kota Malang',
                'Kota Mojokerto', 'Kota Pasuruan', 'Kota Probolinggo', 'Kota Surabaya',
                'Kabupaten Bangkalan', 'Kabupaten Banyuwangi', 'Kabupaten Blitar',
                'Kabupaten Bojonegoro', 'Kabupaten Bondowoso', 'Kabupaten Gresik',
                'Kabupaten Jember', 'Kabupaten Jombang', 'Kabupaten Kediri',
                'Kabupaten Lamongan', 'Kabupaten Lumajang', 'Kabupaten Madiun',
                'Kabupaten Magetan', 'Kabupaten Malang', 'Kabupaten Mojokerto',
                'Kabupaten Nganjuk', 'Kabupaten Ngawi', 'Kabupaten Pacitan',
                'Kabupaten Pamekasan', 'Kabupaten Pasuruan', 'Kabupaten Ponorogo',
                'Kabupaten Probolinggo', 'Kabupaten Sampang', 'Kabupaten Sidoarjo',
                'Kabupaten Situbondo', 'Kabupaten Sumenep', 'Kabupaten Trenggalek',
                'Kabupaten Tuban', 'Kabupaten Tulungagung'
            ]
        ];
    }
}
