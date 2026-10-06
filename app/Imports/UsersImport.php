<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'name'       => $row['nama_pegawai'],
            'nip_pendek' => $row['nip_pendek'],
            'nip'        => $row['nip_panjang'] ?? null,
            'seksi'      => $row['seksi'],
            'jabatan'    => $row['jabatan'],
            'password'   => Hash::make($row['password']),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_pegawai' => 'required|string|max:255',
            'nip_pendek'   => 'required|max:20|unique:users,nip_pendek',
            'nip_panjang'  => 'nullable|max:25|unique:users,nip',
            'seksi'        => 'nullable|string|max:100',
            'jabatan'      => 'nullable|string|max:100',
            'password'     => 'required|min:6',
        ];
    }
}
