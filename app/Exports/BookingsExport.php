<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class BookingsExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithCustomValueBinder, WithStyles
{
    public function __construct(
        public int $startMonth,
        public int $startYear,
        public int $endMonth,
        public int $endYear
    ) {}

    public function query(): Builder
    {
        $startDate = \Carbon\Carbon::create($this->startYear, $this->startMonth, 1)->startOfMonth();
        $endDate   = \Carbon\Carbon::create($this->endYear, $this->endMonth, 1)->endOfMonth();

        return Booking::query()
            ->with(['user', 'vehicle'])
            ->whereBetween('tanggal_mulai', [$startDate, $endDate])
            ->orderBy('tanggal_mulai', 'asc');
    }

    public function headings(): array
    {
        return [
            'ID Booking',
            'NIP',
            'Nama Pegawai',
            'Seksi',
            'Kendaraan',
            'Plat Nomor',
            'Tujuan / Kota',
            'Keperluan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Status Persetujuan',
            'Status Pengembalian'
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->id,
            $booking->user->nip_pendek ?? '-',
            $booking->user->name ?? '-',
            $booking->user->seksi ?? '-',
            $booking->vehicle->nama_kendaraan ?? '-',
            $booking->vehicle->plat_nomor ?? '-',
            $booking->kota ?? '-',
            $booking->keperluan ?? '-',
            $booking->tanggal_mulai ? \Carbon\Carbon::parse($booking->tanggal_mulai)->format('Y-m-d') : '-',
            $booking->tanggal_selesai ? \Carbon\Carbon::parse($booking->tanggal_selesai)->format('Y-m-d') : '-',
            $booking->status->label() ?? '-',
            $booking->status_pengembalian ?? '-'
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // NIP (Column B) and Plat Nomor (Column F) should be strictly TEXT
        $column = $cell->getColumn();
        
        if (in_array($column, ['B', 'F'])) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
