<?php

declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'John Doe', '123456789', '199001012015021001', 'IT', 'Staff', 'password123'
            ],
            [
                'Jane Doe', '987654321', '199201012018022002', 'Keuangan', 'Manager', 'password123'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'nama_pegawai',
            'nip_pendek',
            'nip_panjang',
            'seksi',
            'jabatan',
            'password',
        ];
    }
}
