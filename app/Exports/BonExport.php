<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\BonRequestItem;
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

class BonExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithCustomValueBinder, WithStyles
{
    public function __construct(
        public ?string $status = null,
        public ?string $search = null,
        public ?int $month = null,
        public ?int $year = null
    ) {}

    public function query(): Builder
    {
        return BonRequestItem::query()
            ->with(['bonRequest.user', 'bonItem'])
            ->whereHas('bonRequest', function ($query) {
                if ($this->status) {
                    $query->where('status', $this->status);
                }
                if ($this->month) {
                    $query->whereMonth('created_at', $this->month);
                }
                if ($this->year) {
                    $query->whereYear('created_at', $this->year);
                }
                if ($this->search) {
                    $query->where(function ($q2) {
                        $q2->where('no_bon', 'like', '%' . $this->search . '%')
                           ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'));
                    });
                }
            })
            ->join('bon_requests', 'bon_request_items.bon_request_id', '=', 'bon_requests.id')
            ->orderBy('bon_requests.created_at', 'desc')
            ->select('bon_request_items.*'); // Only select items to prevent ID collision
    }

    public function headings(): array
    {
        return [
            'No. Bon',
            'Tgl. Permintaan',
            'NIP Pemohon',
            'Nama Pemohon',
            'Seksi',
            'Keperluan',
            'Kode Barang',
            'Nama Barang',
            'Jumlah Diminta',
            'Jumlah Diberikan',
            'Satuan',
            'Status',
            'Admin Pemroses',
            'Tgl. Diproses'
        ];
    }

    public function map($item): array
    {
        $req = $item->bonRequest;
        $user = $req->user;
        $admin = $req->processedBy;

        return [
            $req->no_bon ?? '-',
            $req->created_at ? $req->created_at->format('Y-m-d H:i:s') : '-',
            $user->nip_pendek ?? '-',
            $user->name ?? '-',
            $user->seksi ?? '-',
            $req->keperluan ?? '-',
            $item->kode_barang ?? '-',
            $item->nama_barang ?? '-',
            $item->jumlah_diminta ?? 0,
            $item->jumlah_diberikan ?? 0,
            $item->satuan ?? '-',
            ucfirst($req->status ?? '-'),
            $admin->name ?? '-',
            $req->processed_at ? \Carbon\Carbon::parse($req->processed_at)->format('Y-m-d H:i:s') : '-'
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // No Bon (Column A), NIP (Column C), Kode Barang (Column G) strictly TEXT
        $column = $cell->getColumn();
        
        if (in_array($column, ['A', 'C', 'G'])) {
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
