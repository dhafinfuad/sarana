<?php

/*
|--------------------------------------------------------------------------
| Katalog Hak Akses (Admin → Hak Akses)
|--------------------------------------------------------------------------
| Matriks per peran. Peran dihitung otomatis dari jabatan, seksi, dan PLH/PLT
| (lihat User::roles()); satu pengguna bisa punya beberapa peran dan aksesnya
| digabung (OR). 'default' = peran yang diizinkan bila belum diubah di halaman
| Hak Akses (tabel role_permissions hanya menyimpan perubahan dari bawaan).
| Administrator selalu mendapat semua akses. Kunci di 'locked' tidak bisa diubah.
*/

return [

    'roles' => [
        'administrator' => 'Administrator',
        'kepala_kantor' => 'Kepala Kantor',
        'kepala_seksi'  => 'Kepala Seksi',
        'supervisor'    => 'Supervisor',
        'subbag_umum'   => 'Subbag Umum',
        'pkd'           => 'Penjaminan Kualitas Data',
        'pegawai'       => 'Pegawai',
    ],

    'locked' => ['admin.hak_akses'],

    'modules' => [
        'Kendaraan' => [
            'kendaraan.peminjaman'       => ['label' => 'Peminjaman Kendaraan', 'type' => 'Menu & Halaman', 'desc' => 'Ajukan peminjaman kendaraan dinas dan lihat riwayatnya.', 'default' => ['pegawai']],
            'kendaraan.dashboard'        => ['label' => 'Dashboard Persetujuan Peminjaman', 'type' => 'Menu & Halaman', 'desc' => 'Dasbor analitik dan daftar persetujuan peminjaman kendaraan.', 'default' => ['subbag_umum']],
            'kendaraan.dashboard.aksi'   => ['label' => 'Setujui, Tolak, Selesaikan, Edit, Hapus', 'type' => 'Tombol', 'desc' => 'Tombol aksi pada tabel Dashboard Persetujuan Peminjaman.', 'default' => ['subbag_umum']],
            'kendaraan.pengajuan_export' => ['label' => 'Pengajuan & Export', 'type' => 'Tombol', 'desc' => 'Tombol Pengajuan dan Export laporan di Dashboard Persetujuan Peminjaman.', 'default' => ['subbag_umum']],
            'kendaraan.armada'           => ['label' => 'Manajemen Armada', 'type' => 'Menu & Halaman', 'desc' => 'Daftar armada kendaraan dinas.', 'default' => ['subbag_umum']],
            'kendaraan.armada.kelola'    => ['label' => 'Tambah, Ubah, Nonaktifkan Armada', 'type' => 'Tombol', 'desc' => 'Tombol pengelolaan pada halaman Manajemen Armada.', 'default' => ['subbag_umum']],
        ],
        'Permisi' => [
            'permisi.permohonan'         => ['label' => 'Permohonan Izin', 'type' => 'Menu & Halaman', 'desc' => 'Ajukan izin keluar kantor dan lihat riwayatnya.', 'default' => ['pegawai']],
            'permisi.persetujuan'        => ['label' => 'Persetujuan Izin', 'type' => 'Menu & Halaman', 'desc' => 'Setujui atau tolak izin keluar (cakupan bawahan tetap mengikuti struktur organisasi).', 'default' => ['kepala_kantor', 'kepala_seksi', 'supervisor', 'subbag_umum']],
            'permisi.persetujuan.kelola' => ['label' => 'Edit & Hapus Permohonan', 'type' => 'Tombol', 'desc' => 'Tombol Edit dan Hapus di halaman Persetujuan Izin.', 'default' => ['subbag_umum']],
            'permisi.export'             => ['label' => 'Export Laporan Izin', 'type' => 'Tombol', 'desc' => 'Tombol Export (.xlsx) di halaman Persetujuan Izin.', 'default' => ['subbag_umum']],
        ],
        'Ruangan' => [
            'ruangan.peminjaman'         => ['label' => 'Peminjaman Ruangan', 'type' => 'Menu & Halaman', 'desc' => 'Ajukan peminjaman ruangan dan lihat riwayatnya.', 'default' => ['pegawai']],
            'ruangan.persetujuan'        => ['label' => 'Persetujuan Ruangan', 'type' => 'Menu & Halaman', 'desc' => 'Setujui, tolak, edit, dan hapus peminjaman ruangan.', 'default' => ['subbag_umum']],
            'ruangan.kelola'             => ['label' => 'Kelola Ruangan', 'type' => 'Menu & Halaman', 'desc' => 'Tambah, ubah, dan aktif/nonaktifkan ruangan.', 'default' => ['subbag_umum']],
            'ruangan.export'             => ['label' => 'Export Laporan Ruangan', 'type' => 'Tombol', 'desc' => 'Tombol Export (.xlsx) di halaman Persetujuan Ruangan.', 'default' => ['subbag_umum']],
        ],
        'Asmara' => [
            'asmara.permohonan'          => ['label' => 'Permohonan Nomor Surat', 'type' => 'Menu & Halaman', 'desc' => 'Ambil nomor surat keluar dan lihat riwayatnya.', 'default' => ['kepala_kantor', 'kepala_seksi', 'supervisor', 'subbag_umum', 'pkd', 'pegawai']],
            'asmara.admin'               => ['label' => 'Admin Persuratan', 'type' => 'Menu & Halaman', 'desc' => 'Monitoring seluruh surat keluar dan surat kahar.', 'default' => ['subbag_umum', 'pkd']],
        ],
        'Bon ATK' => [
            'bona.permohonan'            => ['label' => 'Permohonan Bon ATK', 'type' => 'Menu & Halaman', 'desc' => 'Katalog ATK dan pengajuan bon.', 'default' => ['pegawai']],
            'bona.kelola'                => ['label' => 'Kelola Bon ATK', 'type' => 'Menu & Halaman', 'desc' => 'Persetujuan dan penyerahan bon ATK.', 'default' => ['subbag_umum']],
            'bona.master'                => ['label' => 'Master Barang & Satuan', 'type' => 'Menu & Halaman', 'desc' => 'Data master barang dan satuan ATK.', 'default' => ['subbag_umum']],
        ],
        'Admin' => [
            'admin.user'                 => ['label' => 'Manajemen User', 'type' => 'Menu & Halaman', 'desc' => 'Kelola akun pegawai.', 'default' => []],
            'admin.log'                  => ['label' => 'Log Aktivitas', 'type' => 'Menu & Halaman', 'desc' => 'Riwayat akses halaman oleh pengguna.', 'default' => []],
            'admin.hak_akses'            => ['label' => 'Hak Akses', 'type' => 'Menu & Halaman', 'desc' => 'Halaman ini. Selalu dan hanya untuk Administrator.', 'default' => []],
        ],
    ],
];
