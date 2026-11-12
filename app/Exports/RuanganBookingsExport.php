<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\RoomBooking;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Laporan permohonan izin keluar per periode (berdasarkan tanggal mulai), mengikuti BookingsExport.
 */
class RuanganBookingsExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithCustomValueBinder, WithStyles
{
    public function __construct(
        public int $startMonth,
        public int $startYear,
        public int $endMonth,
        public int $endYear,
    ) {}

    public function query(): Builder
    {
        $start = \Carbon\Carbon::create($this->startYear, $this->startMonth, 1)->startOfMonth();
        $end   = \Carbon\Carbon::create($this->endYear, $this->endMonth, 1)->endOfMonth();

        return RoomBooking::query()
            ->with(['user', 'room', 'processor'])
            ->whereBetween('tanggal_mulai', [$start, $end])
            ->orderBy('tanggal_mulai', 'asc');
    }

    public function headings(): array
    {
        return [
            'ID Peminjaman', 'NIP', 'Nama Pegawai', 'Seksi', 'Ruangan', 'Lokasi', 'Jumlah Peserta', 'Keperluan',
            'Waktu Mulai', 'Waktu Selesai', 'Status', 'Diproses Oleh', 'Waktu Diproses',
        ];
    }

    /** @param  RoomBooking  $b */
    public function map($b): array
    {
        return [
            $b->id,
            $b->user->nip_pendek ?? '-',
            $b->user->name ?? '-',
            $b->user->seksi ?? '-',
            $b->room->nama ?? '-',
            $b->room->lokasi ?? '-',
            $b->jumlah_peserta,
            $b->keperluan,
            $b->tanggal_mulai?->format('Y-m-d H:i') ?? '-',
            $b->tanggal_selesai?->format('Y-m-d H:i') ?? '-',
            ucfirst($b->status),
            $b->processor->name ?? '-',
            $b->processed_at?->format('Y-m-d H:i') ?? '-',
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // NIP (kolom B) harus tetap teks agar angka nol di depan tidak hilang
        if ($cell->getColumn() === 'B') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
