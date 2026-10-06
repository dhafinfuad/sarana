<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder — Membuat akun demo untuk testing semua role.
 *
 * Setelah menjalankan: php artisan db:seed
 * Login dengan:
 *   Administrator : NIP 1234567890 / password: password
 *   Kepala Kantor : NIP 0987654321 / password: password
 *   Pegawai       : NIP 1122334455 / password: password
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'nip_pendek'      => '123456789',
                'nip'             => '199001012010011001',
                'name'            => 'Admin SARANA',
                'seksi'           => 'Seksi Teknologi Informasi',
                'password'        => Hash::make('password'),
                'jabatan'         => 'Administrator',
                'target_kegiatan' => 0,
            ],
            [
                'nip_pendek'      => '098765432',
                'nip'             => '198001012000011001',
                'name'            => 'Kepala Kantor SARANA',
                'seksi'           => 'Pimpinan',
                'password'        => Hash::make('password'),
                'jabatan'         => 'Kepala Kantor',
                'target_kegiatan' => 0,
            ],
            [
                'nip_pendek'      => '112233445',
                'nip'             => '199501012020011001',
                'name'            => 'Budi Pegawai',
                'seksi'           => 'Seksi Umum',
                'password'        => Hash::make('password'),
                'jabatan'         => 'AR',
                'target_kegiatan' => 4,
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['nip_pendek' => $account['nip_pendek']],
                $account
            );
        }

        $this->command->info('✅ UserSeeder: 3 akun demo berhasil dibuat.');
        $this->command->table(
            ['Role', 'NIP Pendek', 'Password', 'Redirect Ke'],
            [
                ['Administrator', '1234567890', 'password', '/admin/dashboard'],
                ['Kepala Kantor', '0987654321', 'password', '/admin/dashboard'],
                ['Pegawai',       '1122334455', 'password', '/peminjaman'],
            ]
        );
    }
}
