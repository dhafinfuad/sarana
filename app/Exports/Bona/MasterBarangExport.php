<?php

namespace App\Exports\Bona;

use App\Models\BonItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MasterBarangExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return BonItem::with('satuan')->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'Stok Saat Ini',
            'Stok Minimum',
        ];
    }

    /**
     * @param BonItem $item
     */
    public function map($item): array
    {
        return [
            $item->kode_barang,
            $item->nama_barang,
            $item->satuan ? $item->satuan->nama_satuan : '-',
            $item->stok,
            $item->stok_minimum,
        ];
    }
}
