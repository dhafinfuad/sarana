<?php

namespace App\Livewire\Bona;

use App\Models\BonItem;
use App\Models\BonRequest;
use App\Models\BonRequestItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Kelola extends Component
{
    use WithPagination;

    public string $filterStatus = '';
    public string $search = '';

    public string $sortColumn = 'id';
    public string $sortDirection = 'desc';

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    // Modal Detail / Proses
    public bool $showDetailModal  = false;
    public bool $showSetujuiModal = false;
    public bool $showTolakModal   = false;
    public bool $showSerahkanModal = false;
    public bool $showExportModal  = false;

    public ?int $exportMonth = null;
    public ?int $exportYear = null;

    public ?int $selectedRequestId = null;

    // Untuk form approve: jumlah_diberikan per item
    public array $jumlahDiberikan = [];

    // Untuk form tolak
    public string $catatanTolak = '';

    public function mount()
    {
        if (!Auth::user()->canAccessBonKelola()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $this->exportMonth = (int) date('m');
        $this->exportYear = (int) date('Y');
    }

    #[Computed]
    public function requests()
    {
        $query = BonRequest::with(['user', 'items', 'processedBy'])
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, fn($q) => $q->where(function ($q2) {
                $q2->where('no_bon', 'like', '%' . $this->search . '%')
                   ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'));
            }));

        if ($this->sortColumn === 'user.name') {
            $query->join('users', 'bon_requests.user_id', '=', 'users.id')
                  ->orderBy('users.name', $this->sortDirection)
                  ->select('bon_requests.*');
        } elseif ($this->sortColumn === 'id') {
            $query->orderByRaw("FIELD(status, 'menunggu', 'disetujui', 'diserahkan', 'ditolak')")
                  ->orderBy('bon_requests.id', $this->sortDirection);
        } else {
            $query->orderBy('bon_requests.' . $this->sortColumn, $this->sortDirection);
        }

        return $query->paginate(15);
    }

    #[Computed]
    public function selectedRequest()
    {
        if (!$this->selectedRequestId) return null;
        return BonRequest::with(['user', 'items.bonItem'])->find($this->selectedRequestId);
    }

    #[Computed]
    public function stats()
    {
        return [
            'menunggu'   => BonRequest::where('status', 'menunggu')->count(),
            'disetujui'  => BonRequest::where('status', 'disetujui')->count(),
            'diserahkan' => BonRequest::where('status', 'diserahkan')->count(),
            'ditolak'    => BonRequest::where('status', 'ditolak')->count(),
        ];
    }

    public function openDetail(int $id)
    {
        $this->selectedRequestId = $id;
        $request = $this->selectedRequest();

        // Inisialisasi jumlah_diberikan dari jumlah_diminta sebagai default
        $this->jumlahDiberikan = [];
        foreach ($request->items as $item) {
            $this->jumlahDiberikan[$item->id] = $item->jumlah_diminta;
        }

        $this->showDetailModal = true;
    }

    public function openTolak(int $id)
    {
        $this->selectedRequestId = $id;
        $this->catatanTolak = '';
        $this->showTolakModal = true;
    }

    public function openSerahkan(int $id)
    {
        $this->selectedRequestId = $id;
        $this->showSerahkanModal = true;
    }

    /**
     * Admin menyetujui permintaan & memotong stok sesuai jumlah_diberikan
     */
    public function approve()
    {
        $request = BonRequest::with('items.bonItem')->findOrFail($this->selectedRequestId);

        if ($request->status !== 'menunggu') {
            session()->flash('error', 'Permintaan ini sudah diproses sebelumnya.');
            return;
        }

        // Validasi jumlah_diberikan
        foreach ($request->items as $item) {
            $this->validate([
                "jumlahDiberikan.{$item->id}" => [
                    'required', 'integer', 'min:0',
                    "max:{$item->jumlah_diminta}",
                ],
            ], [
                "jumlahDiberikan.{$item->id}.max" => "Jumlah diberikan untuk {$item->nama_barang} tidak boleh melebihi jumlah yang diminta ({$item->jumlah_diminta}).",
                "jumlahDiberikan.{$item->id}.min" => "Jumlah diberikan tidak boleh negatif.",
            ]);
        }

        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                $given = (int) ($this->jumlahDiberikan[$item->id] ?? 0);

                // Update jumlah_diberikan di item
                $item->update(['jumlah_diberikan' => $given]);

                // Potong stok fisik barang
                if ($given > 0 && $item->bonItem) {
                    $item->bonItem->decrement('stok', $given);
                }
            }

            $request->update([
                'status'        => 'disetujui',
                'processed_by'  => Auth::user()->id,
                'processed_at'  => now(),
                'catatan_petugas' => null,
            ]);

            DB::commit();

            $this->showDetailModal = false;
            $this->dispatch('close-setujui-modal');
            session()->flash('success', "Permintaan {$request->no_bon} berhasil disetujui dan stok telah dipotong.");
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Admin menolak permintaan dengan catatan
     */
    public function tolak()
    {
        $this->validate([
            'catatanTolak' => 'required|string|max:500',
        ]);

        $request = BonRequest::findOrFail($this->selectedRequestId);

        if ($request->status !== 'menunggu') {
            session()->flash('error', 'Permintaan ini sudah diproses sebelumnya.');
            return;
        }

        $request->update([
            'status'          => 'ditolak',
            'processed_by'    => Auth::user()->id,
            'processed_at'    => now(),
            'catatan_petugas' => $this->catatanTolak,
        ]);

        $this->showTolakModal = false;
        $this->dispatch('close-tolak-modal');
        session()->flash('success', "Permintaan {$request->no_bon} berhasil ditolak.");
    }

    /**
     * Tandai permintaan sebagai sudah diserahkan secara fisik
     */
    public function tandaiDiserahkan()
    {
        $request = BonRequest::findOrFail($this->selectedRequestId);

        if ($request->status !== 'disetujui') {
            session()->flash('error', 'Hanya permintaan berstatus Disetujui yang bisa ditandai Diserahkan.');
            return;
        }

        $request->update([
            'status'       => 'diserahkan',
            'processed_at' => now(),
        ]);

        $this->showSerahkanModal = false;
        $this->dispatch('close-serahkan-modal');
        session()->flash('success', "Permintaan {$request->no_bon} telah ditandai Diserahkan.");
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function export()
    {
        $this->validate([
            'exportMonth' => 'required|integer|min:1|max:12',
            'exportYear'  => 'required|integer|min:2020|max:2099',
        ]);

        $fileName = 'Laporan_Bon_ATK_' . $this->exportYear . str_pad((string)$this->exportMonth, 2, '0', STR_PAD_LEFT) . '_' . date('His') . '.xlsx';
        
        $this->showExportModal = false;
        $this->dispatch('close-export-modal');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\BonExport($this->filterStatus, $this->search, $this->exportMonth, $this->exportYear), 
            $fileName
        );
    }

    public function render()
    {
        return view('livewire.bona.kelola')->title('Persetujuan Permintaan ATK');
    }
}
