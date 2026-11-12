<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Permisi;
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
class PermisiExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithCustomValueBinder, WithStyles
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

        return Permisi::query()
            ->with(['user', 'processor'])
            ->whereBetween('tanggal_mulai', [$start, $end])
            ->orderBy('tanggal_mulai', 'asc');
    }

    public function headings(): array
    {
        return [
            'ID Permohonan', 'NIP', 'Nama Pegawai', 'Seksi', 'Kategori', 'Jenis', 'Keperluan',
            'Tanggal Mulai', 'Tanggal Selesai', 'Status', 'Diproses Oleh', 'Waktu Diproses',
        ];
    }

    /** @param  Permisi  $permisi */
    public function map($permisi): array
    {
        return [
            $permisi->id,
            $permisi->user->nip_pendek ?? '-',
            $permisi->user->name ?? '-',
            $permisi->user->seksi ?? '-',
            $permisi->kategori,
            $permisi->jenis,
            $permisi->keperluan,
            $permisi->tanggal_mulai?->format('Y-m-d H:i') ?? '-',
            $permisi->tanggal_selesai?->format('Y-m-d H:i') ?? '-',
            ucfirst($permisi->status),
            $permisi->processor->name ?? '-',
            $permisi->processed_at?->format('Y-m-d H:i') ?? '-',
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
