<div x-data="{ showTambahModal: false }"
     x-on:close-tambah-modal.window="showTambahModal = false"
     x-on:open-print-tab.window="window.open($event.detail.url, '_blank')">
    
    @if(!auth()->user()->canAccessAdminDashboard())
        <x-top-header title="Bon ATK" />
    @endif

    <div class="{{ auth()->user()->canAccessAdminDashboard() ? '' : 'py-6 pb-24 md:pb-8 max-w-7xl mx-auto flex-grow w-full px-4 sm:px-6 lg:px-8' }}">
        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">Permintaan ATK</h1>
                <p class="text-slate-500 text-sm mt-1.5">Ajukan permintaan barang ATK baru dan pantau status tiket permohonan.</p>
            </div>

            <button type="button" @click="showTambahModal = true; $wire.openTambah()"
                class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 hover:shadow-brand-600/35 transition flex items-center gap-2 self-start sm:self-auto cursor-pointer">
                <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[11px] font-extrabold" id="cart-counter-badge">
                    {{ count($cart) }}
                </span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>Keranjang</span>
            </button>
        </div>

        {{-- Notification Messages --}}
        @if (session('success'))
            <div role="alert" class="mb-6 p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200/80 shadow-xs flex items-start gap-3 transition-all">
                <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-bold text-emerald-900">Berhasil</p>
                    <p class="text-xs sm:text-sm text-emerald-700 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div role="alert" class="mb-6 p-4 rounded-2xl bg-rose-50/90 border border-rose-200/80 shadow-xs flex items-start gap-3 transition-all">
                <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-bold text-rose-900">Terjadi Kesalahan</p>
                    <p class="text-xs sm:text-sm text-rose-700 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Bon Orders Table Container -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden mb-12" wire:poll.30s>
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Riwayat Pengajuan Tiket Bon ATK</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Daftar permohonan yang telah diajukan ke Subbagian Umum</p>
                </div>

                <div class="flex items-center gap-2.5 w-full sm:w-auto">
                    <div class="relative flex-1 sm:w-60">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <span class="material-symbols-outlined text-[16px]">search</span>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="searchRiwayat"
                            class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-9 pr-3 py-1.5 text-xs text-slate-800 focus:bg-white focus:border-brand-500 outline-none transition"
                            placeholder="Cari tiket/keperluan...">
                    </div>
                    <div class="shrink-0 w-32 sm:w-40">
                        <select wire:model.live="filterStatusRiwayat"
                            class="w-full bg-slate-50/60 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 outline-none transition">
                            <option value="">Semua Status</option>
                            <option value="menunggu">Menunggu</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="diserahkan">Diserahkan</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th wire:click="sortBy('no_bon')" class="py-3.5 px-6 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Tiket</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'no_bon' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'no_bon' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'no_bon' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('created_at')" class="py-3.5 px-4 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Tanggal</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'created_at' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'created_at' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'created_at' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('keperluan')" class="py-3.5 px-4 cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    <span>Keperluan</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'keperluan' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'keperluan' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'keperluan' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th class="py-3.5 px-4">Item Permintaan</th>
                            <th wire:click="sortBy('status')" class="py-3.5 px-4 text-center whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center justify-center gap-1">
                                    <span>Status</span>
                                    <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'status' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                        {{ $sortColumn === 'status' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'status' ? 'arrow_downward' : 'swap_vert') }}
                                    </span>
                                </div>
                            </th>
                            <th class="py-3.5 px-6 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->requests as $req)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <code class="font-mono font-bold text-brand-700 bg-brand-50 px-2.5 py-1 rounded-md border border-brand-100 text-[11px] inline-block">
                                        {{ $req->no_bon }}
                                    </code>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap text-slate-500 font-medium">
                                    {{ $req->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-4 px-4 text-slate-800 font-medium max-w-xs">
                                    {{ $req->keperluan }}
                                </td>
                                <td class="py-4 px-4 text-slate-600">
                                    <ul class="list-disc pl-4 space-y-0.5">
                                        @foreach($req->items as $item)
                                            <li>{{ $item->nama_barang }} ({{ $item->jumlah_diminta }} {{ $item->satuan }})</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @php
                                        $statusPill = match($req->status) {
                                            'menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'disetujui' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'diserahkan' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default => 'bg-slate-100 text-slate-600 border-slate-200'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $statusPill }}">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-1.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center justify-center gap-0.5">
                                        <a href="{{ route('bona.print', $req->id) }}" target="_blank"
                                            class="p-1 text-slate-600 hover:text-brand-600 hover:bg-brand-50 rounded-lg border border-transparent hover:border-brand-200 transition cursor-pointer shrink-0 inline-flex items-center justify-center"
                                            title="Cetak Form">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-1.5">
                                        <span class="material-symbols-outlined text-[32px] text-slate-300">receipt_long</span>
                                        <p class="font-semibold text-slate-600">Anda belum pernah membuat permintaan ATK.</p>
                                        <p class="text-xs text-slate-400">Pilih barang dari katalog di bawah untuk mengajukan permohonan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-3 p-4">
                @forelse($this->requests as $req)
                    @php
                        $statusPill = match($req->status) {
                            'menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'disetujui' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'diserahkan' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-slate-100 text-slate-600 border-slate-200'
                        };
                    @endphp
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-card flex flex-col gap-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                            <div>
                                <code class="font-mono font-bold text-xs text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-100">
                                    {{ $req->no_bon }}
                                </code>
                                <p class="text-[11px] text-slate-400 mt-1">{{ $req->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $statusPill }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </div>

                        <div class="text-xs space-y-2">
                            <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Keperluan:</p>
                                <p class="font-semibold text-slate-800 leading-snug">{{ $req->keperluan }}</p>
                            </div>

                            <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-1">Daftar Item ATK:</p>
                                <ul class="list-disc pl-4 space-y-0.5 text-slate-700">
                                    @foreach($req->items as $item)
                                        <li>{{ $item->nama_barang }} ({{ $item->jumlah_diminta }} {{ $item->satuan }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-1.5 pt-2 border-t border-slate-100">
                            <a href="{{ route('bona.print', $req->id) }}" target="_blank"
                                class="p-2 border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl transition cursor-pointer flex items-center justify-center shadow-2xs" title="Cetak Form">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        <p>Anda belum pernah membuat permintaan ATK.</p>
                    </div>
                @endforelse
            </div>
            
            @if($this->requests->hasPages('riwayatPage'))
                <div class="px-6 py-4 border-t border-slate-100 bg-white">
                    {{ $this->requests->links() }}
                </div>
            @endif
        </div>

        <!-- Section: Katalog Barang ATK Header -->
        <div class="mb-6">
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Katalog Barang ATK</h2>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">Cari dan pilih barang yang Anda butuhkan untuk operasional dinas.</p>
        </div>

        <!-- Catalog Container -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-80">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">search</span>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="searchKatalog"
                            class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all"
                            placeholder="Cari nama atau kode barang...">
                    </div>
                    
                    <label class="w-full sm:w-auto flex items-center justify-center sm:justify-start gap-2 cursor-pointer px-3.5 py-2 border rounded-xl shadow-2xs select-none transition-colors border-slate-200 bg-slate-50/60 text-slate-700 hover:bg-slate-100">
                        <input type="checkbox" wire:model.live="filterStokMenipis" class="rounded text-brand-600 focus:ring-brand-500">
                        <span class="text-xs font-semibold whitespace-nowrap">Stok Menipis</span>
                    </label>
                    
                    <select wire:model.live="perPageKatalog"
                        class="w-full sm:w-auto bg-slate-50/60 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:border-brand-500 outline-none transition">
                        <option value="50">50 Baris</option>
                        <option value="100">100 Baris</option>
                    </select>
                </div>
                
                <div class="text-xs text-slate-400 font-medium" wire:loading wire:target="searchKatalog, filterStokMenipis, perPageKatalog">
                    Memuat katalog...
                </div>
            </div>
            
            <div class="p-6 bg-slate-50/50">
                @if(count($this->bonItems) > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($this->bonItems as $item)
                            <div class="bg-white rounded-3xl p-3.5 border border-slate-200/90 shadow-card hover:shadow-lg transition flex flex-col justify-between group" wire:key="item-{{ $item->id }}">
                                <div>
                                    <div class="relative w-full h-28 bg-slate-50 rounded-2xl flex items-center justify-center p-2 mb-3 overflow-hidden border border-slate-100">
                                        <span class="absolute top-2 right-2 z-10 px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/95 backdrop-blur-xs {{ $item->stok_tersedia <= 10 ? 'text-rose-600 border-rose-200' : 'text-brand-700 border-slate-200' }} border shadow-2xs pointer-events-none">
                                            Stok: {{ $item->stok_tersedia }} {{ $item->satuan ? $item->satuan->nama_satuan : 'PCS' }}
                                        </span>
                                        @if($item->image_path)
                                            <img class="w-full h-full object-contain group-hover:scale-105 transition duration-300" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->nama_barang }}">
                                        @else
                                            <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        @endif
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-800 leading-snug line-clamp-2" title="{{ $item->nama_barang }}">
                                        {{ $item->nama_barang }}
                                    </h4>
                                    
                                    @if(session()->has('success_cart_' . $item->id))
                                        <div class="mt-1 text-[11px] text-emerald-600 font-bold">Ditambahkan!</div>
                                    @endif
                                    @if(session()->has('error_cart_' . $item->id))
                                        <div class="mt-1 text-[10px] text-rose-600 font-semibold leading-tight">{{ session('error_cart_' . $item->id) }}</div>
                                    @endif
                                </div>

                                <div class="pt-3 mt-3 border-t border-slate-100 flex items-center gap-1.5">
                                    <input type="number" wire:model="inputQty.{{ $item->id }}" min="1"
                                        class="w-16 px-2 py-1.5 bg-slate-50 text-center font-bold text-xs rounded-xl border border-slate-200 outline-none focus:bg-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20 transition" placeholder="Qty">
                                    <button type="button" wire:click="addToCart({{ $item->id }})"
                                        class="flex-1 py-1.5 bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white rounded-xl text-xs font-bold flex items-center justify-center shadow-xs transition cursor-pointer"
                                        title="Tambah ke keranjang" wire:loading.attr="disabled" wire:target="addToCart({{ $item->id }})">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-slate-400">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">inventory_2</span>
                        <p class="font-semibold text-slate-600">Barang tidak ditemukan.</p>
                    </div>
                @endif
            </div>
            
            @if($this->bonItems->hasPages('katalogPage'))
                <div class="px-6 py-4 border-t border-slate-100 bg-white">
                    {{ $this->bonItems->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Form Permintaan / Keranjang -->
        <x-modal show="showTambahModal" maxWidth="max-w-lg" :dismissable="false">
            <x-slot:header>
                <div class="flex justify-between items-center w-full">
                    <h2 class="text-base font-bold text-slate-900">Keranjang Permintaan ATK</h2>
                    <button type="button" @click="showTambahModal = false" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                        &times;
                    </button>
                </div>
            </x-slot:header>
            
            <div class="space-y-4 text-xs">
                <div>
                    <h3 class="font-bold text-slate-700 uppercase tracking-wider mb-2">Item di Keranjang</h3>
                    
                    <div class="bg-slate-50/60 border border-slate-200 rounded-2xl overflow-y-auto max-h-80 p-2">
                        @forelse($cart as $index => $c)
                            <div class="flex items-start justify-between p-2.5 border-b border-slate-100 last:border-0 hover:bg-white rounded-xl transition">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 mr-3">
                                        @if($c['image_path'])
                                            <img src="{{ asset('storage/' . $c['image_path']) }}" class="h-10 w-10 rounded-xl object-cover border border-slate-200">
                                        @else
                                            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center border border-slate-200">
                                                <span class="material-symbols-outlined text-slate-400 text-[18px]">inventory_2</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 leading-snug">{{ $c['nama_barang'] }}</h4>
                                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $c['jumlah_diminta'] }} {{ $c['satuan'] }}</p>
                                        @if($c['catatan'])
                                            <p class="text-[10px] text-slate-400 italic">Catatan: {{ $c['catatan'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <button type="button" wire:click="removeFromCart({{ $index }})" class="text-rose-500 hover:text-rose-700 p-1" title="Hapus">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center text-slate-400 py-8">
                                <span class="material-symbols-outlined text-4xl mb-1.5 opacity-40">shopping_basket</span>
                                <p class="text-xs font-semibold">Keranjang masih kosong</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <form wire:submit.prevent="submitPermohonan" id="formSubmit">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Untuk Keperluan <span class="text-rose-500">*</span></label>
                        <textarea wire:model="keperluan" rows="3"
                            class="w-full p-3 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition resize-none"
                            placeholder="Tuliskan tujuan permintaan ATK ini..."></textarea>
                        @error('keperluan') <span class="mt-1 text-[11px] font-medium text-rose-600 block">{{ $message }}</span> @enderror
                    </div>
                </form>
            </div>

            <x-slot:footer>
                <div class="flex justify-end gap-2.5 w-full">
                    <button type="button" @click="showTambahModal = false"
                        class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="submitPermohonan"
                        class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ empty($cart) ? 'disabled' : '' }} wire:loading.attr="disabled">
                        Kirim Permintaan
                    </button>
                </div>
            </x-slot:footer>
        </x-modal>
    </div>
</div>
