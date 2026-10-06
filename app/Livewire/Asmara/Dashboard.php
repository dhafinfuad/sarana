<?php

namespace App\Livewire\Asmara;

use App\Models\JenisPj;
use App\Models\JenisSurat;
use App\Models\SuratKeluar;
use App\Models\TblNomorAwal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
class Dashboard extends Component
{
    use WithPagination;

    public bool $isAdminMode = false;

    // Filters
    public $search = '';
    public $filterTahun = '';
    public $filterJenis = '';
    public $searchNomorAwal = '';

    public string $sortColumn = 'tgl_surat';
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

    // Modals visibility
    public bool $showFormModal = false;
    public bool $showNomorAwalModal = false;

    // Form fields for Surat Keluar
    public $editSuratId = null;
    public $jenisSurat = '';
    public $jenisPj = '/KPP.1209/';
    public $tahunSurat = '';
    public $tglSurat = '';
    public $perihal = '';
    public $tujuanSurat = '';
    public $keterangan = '';
    public $duplikasi = 1;
    public $batal = '';
    
    // Form fields for TblNomorAwal
    public $editNomorAwalId = null;
    public $nomorAwalJenisSurat = '';
    public $nomorAwalJenisPj = '/KPP.1209/';
    public $nomorAwalTahun = '';
    public $nomorAwalAngka = 1;

    public function mount()
    {
        // Default values
        $this->tahunSurat = date('Y');
        $this->tglSurat = date('Y-m-d');
        $this->nomorAwalTahun = date('Y');
        
        $this->filterTahun = date('Y');
        
        // Determine mode based on route
        if (request()->routeIs('asmara.admin')) {
            if (!Auth::user()->canAccessAsmaraAdmin()) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            }
            $this->isAdminMode = true;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterTahun()
    {
        $this->resetPage();
    }
    public function updatingFilterJenis()
    {
        $this->resetPage();
    }

    #[Computed]
    public function daftarJenisSurat()
    {
        $existingFromMaster = JenisSurat::pluck('jenis_surat')->toArray();
        $fromNomorAwal = TblNomorAwal::select('jenis_surat')->whereNotNull('jenis_surat')->where('jenis_surat', '!=', '')->distinct()->pluck('jenis_surat')->toArray();
        $fromSuratKeluar = SuratKeluar::select('jenis_surat')->whereNotNull('jenis_surat')->where('jenis_surat', '!=', '')->distinct()->pluck('jenis_surat')->toArray();

        $allJenis = array_unique(array_filter(array_merge($existingFromMaster, $fromNomorAwal, $fromSuratKeluar)));

        foreach ($allJenis as $j) {
            if ($j && !in_array($j, $existingFromMaster)) {
                JenisSurat::firstOrCreate(['jenis_surat' => trim($j)]);
            }
        }

        return JenisSurat::orderBy('jenis_surat')->get();
    }

    #[Computed]
    public function daftarJenisPj()
    {
        return JenisPj::orderBy('jenis_pj')->get();
    }

    #[Computed]
    public function daftarNomorAwal()
    {
        return TblNomorAwal::when($this->searchNomorAwal, function ($query) {
                $term = trim((string) $this->searchNomorAwal);
                $driver = DB::connection()->getDriverName();
                $concatNomorAwal = $driver === 'sqlite'
                    ? "COALESCE(jenis_surat, '') || COALESCE(nomor_awal, '') || COALESCE(jenis_pj, '') || COALESCE(tahun, '')"
                    : "CONCAT(COALESCE(jenis_surat, ''), COALESCE(nomor_awal, ''), COALESCE(jenis_pj, ''), COALESCE(tahun, ''))";

                $query->where(function ($sub) use ($term, $concatNomorAwal) {
                    $sub->where('jenis_surat', 'like', '%' . $term . '%')
                        ->orWhere('jenis_pj', 'like', '%' . $term . '%')
                        ->orWhere('tahun', 'like', '%' . $term . '%')
                        ->orWhere('nomor_awal', 'like', '%' . $term . '%')
                        ->orWhereRaw("{$concatNomorAwal} LIKE ?", ['%' . $term . '%']);

                    $cleanTerm = str_replace(['-', ' '], '', $term);
                    if ($cleanTerm !== '') {
                        $sub->orWhereRaw("REPLACE(REPLACE({$concatNomorAwal}, '-', ''), ' ', '') LIKE ?", ['%' . $cleanTerm . '%']);
                    }
                });
            })
            ->orderBy('tahun', 'desc')->orderBy('jenis_surat')->get();
    }

    #[Computed]
    public function listSurat()
    {
        $query = SuratKeluar::with('user')
            ->search($this->search)
            ->when($this->filterTahun, fn($q) => $q->where('tahun_surat', $this->filterTahun))
            ->when($this->filterJenis, fn($q) => $q->where('jenis_surat', $this->filterJenis));

        if (!$this->isAdminMode) {
            $query->where('perekam', Auth::user()->nip_pendek);
        }

        return $query->orderBy($this->sortColumn, $this->sortDirection)
            ->orderBy('id_surat_keluar', 'desc')
            ->paginate(20);
    }

    // ------------------------------------------------------------------------
    // Aksi Surat Keluar
    // ------------------------------------------------------------------------

    public function createSurat()
    {
        $this->resetSuratForm();
        $this->showFormModal = true;
    }

    public function editSurat($id)
    {
        $surat = SuratKeluar::findOrFail($id);
        
        // Check permission: only admin or the creator can edit
        if (!$this->isAdminMode && $surat->perekam !== Auth::user()->nip_pendek) {
            $this->dispatch('notify', title: 'Error', message: 'Anda tidak berhak mengedit surat ini.', type: 'error');
            return;
        }

        $this->editSuratId = $surat->id_surat_keluar;
        $this->jenisSurat = $surat->jenis_surat;
        $this->jenisPj = $surat->jenis_pj;
        $this->tahunSurat = $surat->tahun_surat;
        $this->tglSurat = $surat->tgl_surat->format('Y-m-d');
        $this->perihal = $surat->perihal;
        $this->tujuanSurat = $surat->tujuan_surat;
        $this->keterangan = $surat->keterangan;
        $this->batal = $surat->batal;
        $this->duplikasi = 1; // Duplikasi not applicable for edit

        $this->showFormModal = true;
    }

    public function simpanSurat()
    {
        $this->validate([
            'jenisSurat' => 'required',
            'jenisPj' => 'required',
            'tahunSurat' => 'required|integer',
            'tglSurat' => 'required|date',
            'perihal' => 'required',
            'tujuanSurat' => 'required',
        ]);

        if ($this->jenisSurat) {
            JenisSurat::firstOrCreate(['jenis_surat' => trim($this->jenisSurat)]);
        }

        if ($this->editSuratId) {
            // EDIT MODE
            $surat = SuratKeluar::findOrFail($this->editSuratId);
            $surat->update([
                'jenis_surat' => $this->jenisSurat,
                'jenis_pj' => $this->jenisPj,
                'tahun_surat' => $this->tahunSurat,
                'tgl_surat' => $this->tglSurat,
                'perihal' => $this->perihal,
                'tujuan_surat' => $this->tujuanSurat,
                'keterangan' => $this->keterangan,
                'batal' => $this->batal === '1' ? '1' : '',
            ]);
            
            $this->dispatch('notify', title: 'Sukses', message: 'Surat berhasil diperbarui.', type: 'success');
            session()->flash('success_message', 'Surat keluar berhasil diedit.');
        } else {
            // CREATE MODE
            $duplikasiCount = (int) $this->duplikasi;
            
            for ($i = 0; $i < $duplikasiCount; $i++) {
                $nextNo = $this->getNomorBerikutnya($this->jenisSurat, $this->jenisPj, $this->tahunSurat);
                
                SuratKeluar::create([
                    'jenis_surat' => $this->jenisSurat,
                    'jenis_pj' => $this->jenisPj,
                    'nomor_surat' => $nextNo,
                    'tahun_surat' => $this->tahunSurat,
                    'tgl_surat' => $this->tglSurat,
                    'perihal' => $this->perihal,
                    'tujuan_surat' => $this->tujuanSurat,
                    'perekam' => Auth::user()->nip_pendek,
                    'tgl_rekam' => date('Y-m-d'),
                    'keterangan' => $this->keterangan,
                    'batal' => $this->batal === '1' ? '1' : '',
                ]);
            }
            
            $this->dispatch('notify', title: 'Sukses', message: "Berhasil mengambil $duplikasiCount nomor surat.", type: 'success');
            session()->flash('success_message', 'Surat keluar berhasil disimpan.');
        }

        $this->showFormModal = false;
        $this->resetSuratForm();
    }

    public function deleteSurat($id)
    {
        $surat = SuratKeluar::findOrFail($id);
        
        if (!$this->isAdminMode && $surat->perekam !== Auth::user()->nip_pendek) {
            $this->dispatch('notify', title: 'Error', message: 'Anda tidak berhak menghapus surat ini.', type: 'error');
            return;
        }

        $surat->delete();
        $this->dispatch('notify', title: 'Sukses', message: 'Surat berhasil dihapus.', type: 'success');
        session()->flash('success_message', 'Surat keluar berhasil dihapus.');
    }

    private function getNomorBerikutnya($jSurat, $jPj, $tahun)
    {
        // 1. Cek dari tbl_nomor_awal
        $nomorAwalRecord = TblNomorAwal::where('jenis_surat', $jSurat)
            ->where('jenis_pj', $jPj)
            ->where('tahun', $tahun)
            ->first();
            
        $nomorAwal = $nomorAwalRecord ? (int)$nomorAwalRecord->nomor_awal : 0;

        // 2. Cek MAX dari surat_keluar
        $maxSurat = SuratKeluar::where('jenis_surat', $jSurat)
            ->where('jenis_pj', $jPj)
            ->where('tahun_surat', $tahun)
            ->max('nomor_surat');
            
        $maxSurat = $maxSurat ? (int)$maxSurat : 0;

        // 3. Return yang terbesar
        if ($nomorAwalRecord && $nomorAwal > $maxSurat) {
            return $nomorAwal;
        }
        
        return $maxSurat + 1;
    }

    #[Computed]
    public function previewNomor()
    {
        if (!$this->jenisSurat || !$this->tahunSurat) return '-';
        
        // Don't calculate for edit mode to save queries, just show current
        if ($this->editSuratId) {
            return "Edit mode: Nomor tidak berubah";
        }
        
        $nextNo = $this->getNomorBerikutnya($this->jenisSurat, $this->jenisPj, $this->tahunSurat);
        return $this->jenisSurat . $nextNo . $this->jenisPj . $this->tahunSurat;
    }

    private function resetSuratForm()
    {
        $this->editSuratId = null;
        $this->jenisSurat = '';
        $this->jenisPj = '/KPP.1209/';
        $this->tahunSurat = date('Y');
        $this->tglSurat = date('Y-m-d');
        $this->perihal = '';
        $this->tujuanSurat = '';
        $this->keterangan = '';
        $this->duplikasi = 1;
        $this->batal = '';
    }

    // ------------------------------------------------------------------------
    // Aksi Nomor Awal (Kahar)
    // ------------------------------------------------------------------------

    public function openNomorAwalModal()
    {
        $this->resetNomorAwalForm();
        $this->showNomorAwalModal = true;
    }

    public function editNomorAwal($id)
    {
        $na = TblNomorAwal::findOrFail($id);
        $this->editNomorAwalId = $na->id;
        $this->nomorAwalJenisSurat = $na->jenis_surat;
        $this->nomorAwalJenisPj = $na->jenis_pj;
        $this->nomorAwalTahun = $na->tahun;
        $this->nomorAwalAngka = $na->nomor_awal;
    }

    public function simpanNomorAwal()
    {
        if (!Auth::user()->canAccessAsmaraAdmin()) return;

        $this->validate([
            'nomorAwalJenisSurat' => 'required',
            'nomorAwalTahun' => 'required|integer',
            'nomorAwalAngka' => 'required|integer|min:1',
        ]);

        if ($this->nomorAwalJenisSurat) {
            JenisSurat::firstOrCreate(['jenis_surat' => trim($this->nomorAwalJenisSurat)]);
        }

        if ($this->editNomorAwalId) {
            TblNomorAwal::findOrFail($this->editNomorAwalId)->update([
                'jenis_surat' => $this->nomorAwalJenisSurat,
                'jenis_pj' => $this->nomorAwalJenisPj ?? '',
                'tahun' => $this->nomorAwalTahun,
                'nomor_awal' => $this->nomorAwalAngka,
            ]);
            $this->dispatch('notify', title: 'Sukses', message: 'Nomor Awal berhasil diupdate.', type: 'success');
            session()->flash('success_message', 'Nomor surat kahar berhasil diedit.');
            $this->resetNomorAwalForm();
        } else {
            // Check if exists
            $exists = TblNomorAwal::where('jenis_surat', $this->nomorAwalJenisSurat)
                ->where('jenis_pj', $this->nomorAwalJenisPj ?? '')
                ->where('tahun', $this->nomorAwalTahun)
                ->first();
                
            if ($exists) {
                $this->dispatch('notify', title: 'Error', message: 'Kombinasi Nomor Awal tersebut sudah ada!', type: 'error');
                return;
            }

            TblNomorAwal::create([
                'jenis_surat' => $this->nomorAwalJenisSurat,
                'jenis_pj' => $this->nomorAwalJenisPj ?? '',
                'tahun' => $this->nomorAwalTahun,
                'nomor_awal' => $this->nomorAwalAngka,
            ]);
            $this->dispatch('notify', title: 'Sukses', message: 'Nomor Awal berhasil ditambahkan.', type: 'success');
            session()->flash('success_message', 'Nomor surat kahar berhasil disimpan.');
            $this->resetNomorAwalForm();
        }
    }

    public function deleteNomorAwal($id)
    {
        if (!Auth::user()->canAccessAsmaraAdmin()) return;
        TblNomorAwal::findOrFail($id)->delete();
        $this->dispatch('notify', title: 'Sukses', message: 'Nomor Awal berhasil dihapus.', type: 'success');
        session()->flash('success_message', 'Nomor surat kahar berhasil dihapus.');
    }

    private function resetNomorAwalForm()
    {
        $this->editNomorAwalId = null;
        $this->nomorAwalJenisSurat = '';
        $this->nomorAwalJenisPj = '/KPP.1209/';
        $this->nomorAwalTahun = date('Y');
        $this->nomorAwalAngka = 1;
    }

    public function render()
    {
        $layout = Auth::user()->canAccessAdminDashboard()
            ? 'components.layouts.admin'
            : 'components.layouts.app';

        $title = $this->isAdminMode ? 'Semua Surat Keluar — Asmara' : 'Riwayat Surat Saya — Asmara';

        return view('livewire.asmara.dashboard')->layout($layout)->title($title);
    }
}
