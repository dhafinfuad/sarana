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

class Permohonan extends Component
{
    use WithPagination;

    public bool $showTambahModal = false;

    // Cart (Keranjang Permintaan)
    public array $cart = [];

    // Form Keranjang (Grid)
    public array $inputQty = [];
    public array $inputCatatan = [];
    
    // Filter Riwayat
    public string $searchRiwayat = '';
    public string $filterStatusRiwayat = '';
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

    // Filter Katalog
    public string $searchKatalog = '';
    public bool $filterStokMenipis = false;
    public int $perPageKatalog = 50;
    
    // Form Utama
    public string $keperluan = '';

    public function mount()
    {
        if (!Auth::user()->canAccessBonPermohonan()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    #[Computed]
    public function bonItems()
    {
        $query = BonItem::with('satuan')
            ->withSum(['requestItems as total_booked' => function ($q) {
                $q->whereHas('bonRequest', function ($sq) {
                    $sq->where('status', 'Menunggu');
                });
            }], 'jumlah_diminta');

        if ($this->searchKatalog) {
            $query->where(function($q) {
                $q->where('nama_barang', 'like', '%' . $this->searchKatalog . '%')
                  ->orWhere('kode_barang', 'like', '%' . $this->searchKatalog . '%');
            });
        }

        $items = $query->get()->map(function ($item) {
            $item->stok_tersedia = $item->stok - ($item->total_booked ?? 0);
            return $item;
        })->filter(function ($item) {
            if ($this->filterStokMenipis) {
                return $item->stok_tersedia > 0 && $item->stok_tersedia <= 10;
            }
            return $item->stok_tersedia > 0;
        })->sortBy('nama_barang')->values();

        // Manual pagination for the filtered collection
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('katalogPage');
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->forPage($page, $this->perPageKatalog),
            $items->count(),
            $this->perPageKatalog,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'katalogPage']
        );

        return $paginator;
    }

    #[Computed]
    public function requests()
    {
        $query = BonRequest::with('items')
                         ->where('user_id', Auth::user()->id);
                         
        if ($this->searchRiwayat) {
            $query->where(function($q) {
                $q->where('no_bon', 'like', '%' . $this->searchRiwayat . '%')
                  ->orWhere('keperluan', 'like', '%' . $this->searchRiwayat . '%');
            });
        }
        
        if ($this->filterStatusRiwayat) {
            $query->where('status', $this->filterStatusRiwayat);
        }
        
        return $query->orderBy($this->sortColumn, $this->sortDirection)->paginate(5, ['*'], 'riwayatPage');
    }

    public function openTambah()
    {
        $this->showTambahModal = true;
    }

    public function addToCart($itemId)
    {
        $jumlah = $this->inputQty[$itemId] ?? null;
        $catatan = $this->inputCatatan[$itemId] ?? '';

        if (!$jumlah || $jumlah < 1) {
            session()->flash('error_cart_' . $itemId, 'Jumlah harus minimal 1.');
            return;
        }

        // Get item from full collection since it might be on another page
        $item = BonItem::with('satuan')
            ->withSum(['requestItems as total_booked' => function ($q) {
                $q->whereHas('bonRequest', function ($sq) {
                    $sq->where('status', 'Menunggu');
                });
            }], 'jumlah_diminta')
            ->find($itemId);

        if (!$item) {
            session()->flash('error_cart_' . $itemId, 'Barang tidak ditemukan.');
            return;
        }

        $item->stok_tersedia = $item->stok - ($item->total_booked ?? 0);

        if ($jumlah > $item->stok_tersedia) {
            session()->flash('error_cart_' . $itemId, 'Jumlah melebihi stok yang tersedia (' . $item->stok_tersedia . ').');
            return;
        }

        // Cek apakah barang sudah ada di keranjang
        $existingIndex = collect($this->cart)->search(fn($c) => $c['item_id'] == $item->id);

        if ($existingIndex !== false) {
            // Update jika sudah ada
            $newQty = $this->cart[$existingIndex]['jumlah_diminta'] + $jumlah;
            if ($newQty > $item->stok_tersedia) {
                session()->flash('error_cart_' . $itemId, 'Total jumlah di keranjang melebihi stok (' . $item->stok_tersedia . ').');
                return;
            }
            $this->cart[$existingIndex]['jumlah_diminta'] = $newQty;
            if ($catatan) {
                $this->cart[$existingIndex]['catatan'] = $catatan;
            }
        } else {
            // Tambah baru
            $this->cart[] = [
                'item_id' => $item->id,
                'kode_barang' => $item->kode_barang,
                'nama_barang' => $item->nama_barang,
                'image_path' => $item->image_path,
                'satuan' => $item->satuan ? $item->satuan->nama_satuan : 'PCS',
                'stok_tersedia' => $item->stok_tersedia,
                'jumlah_diminta' => $jumlah,
                'catatan' => $catatan,
            ];
        }

        // Reset input card tersebut
        unset($this->inputQty[$itemId]);
        unset($this->inputCatatan[$itemId]);
        
        session()->flash('success_cart_' . $itemId, 'Berhasil ditambahkan ke keranjang.');
    }

    public function removeFromCart($index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart); // Re-index array
        }
    }

    private function generateNomorTiket(): string
    {
        $today = now()->format('Ymd');
        $last = BonRequest::where('nomor_tiket', 'like', 'BON-' . $today . '-%')
                          ->orderBy('id', 'desc')
                          ->first();
                          
        $lastNum = 0;
        if ($last) {
            $parts = explode('-', $last->nomor_tiket);
            if (count($parts) === 3) {
                $lastNum = (int) $parts[2];
            }
        }
        
        return 'BON-' . $today . '-' . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
    }

    public function submitPermohonan()
    {
        // Validasi keperluan dulu sebelum cek cart
        $this->validate([
            'keperluan' => 'required|string|max:255',
        ]);

        if (empty($this->cart)) {
            $this->addError('keperluan', 'Keranjang permintaan tidak boleh kosong.');
            return;
        }

        DB::beginTransaction();
        try {
            // 1. Buat Header — gunakan kolom 'no_bon' sesuai skema DB
            $bonRequest = BonRequest::create([
                'user_id'   => Auth::user()->id,
                'no_bon'    => BonRequest::generateNoBon(),
                'keperluan' => $this->keperluan,
                'status'    => 'menunggu',
            ]);

            // 2. Buat Items — hanya kolom yang ada di tabel
            foreach ($this->cart as $cartItem) {
                BonRequestItem::create([
                    'bon_request_id' => $bonRequest->id,
                    'bon_item_id'    => $cartItem['item_id'],
                    'nama_barang'    => $cartItem['nama_barang'],
                    'satuan'         => $cartItem['satuan'],
                    'jumlah_diminta' => $cartItem['jumlah_diminta'],
                    'jumlah_diberikan' => null,
                ]);
            }

            DB::commit();

            $this->showTambahModal = false;
            $this->dispatch('close-tambah-modal');
            $this->reset(['cart', 'inputQty', 'inputCatatan', 'keperluan']);
            session()->flash('success', 'Permintaan Bon ATK berhasil dikirim. No. Bon: ' . $bonRequest->no_bon);
            
            // Dispatch event untuk buka tab print
            $this->dispatch('open-print-tab', url: route('bona.print', $bonRequest->id));
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $layout = Auth::user()->canAccessAdminDashboard() ? 'components.layouts.admin' : 'components.layouts.app';
        return view('livewire.bona.permohonan')->title('Permintaan ATK')->layout($layout);
    }
}
