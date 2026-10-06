<div x-data="{ showSetujuiModal: false, showTolakModal: false, showExportModal: false, showSerahkanModal: false }"
     x-on:close-setujui-modal.window="showSetujuiModal = false"
     x-on:close-tolak-modal.window="showTolakModal = false"
     x-on:close-serahkan-modal.window="showSerahkanModal = false"
     x-on:close-export-modal.window="showExportModal = false">

    {{-- Page Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">Kelola Persetujuan ATK</h1>
            <p class="text-slate-500 text-sm mt-1.5">Setujui, tolak, atau tandai penyerahan permintaan barang ATK dinas.</p>
        </div>

        <button type="button" @click="showExportModal = true"
            class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 hover:shadow-brand-600/35 transition flex items-center gap-2 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            <span>Export Excel</span>
        </button>
    </div>

    {{-- Notification Messages --}}
    @if(session('success'))
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
    @if(session('error'))
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

    {{-- 4 Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Card 1: Menunggu -->
        <button type="button" wire:click="$set('filterStatus', 'menunggu')"
            class="text-left bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 shadow-card hover:shadow-lg cursor-pointer relative overflow-hidden group flex items-center justify-between gap-2 sm:gap-3 {{ $filterStatus === 'menunggu' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200/90 hover:border-slate-300' }}">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">MENUNGGU</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-2 tracking-tight">{{ $this->stats['menunggu'] }}</p>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100 transition-transform duration-200 group-hover:scale-105">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </button>

        <!-- Card 2: Disetujui -->
        <button type="button" wire:click="$set('filterStatus', 'disetujui')"
            class="text-left bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 shadow-card hover:shadow-lg cursor-pointer relative overflow-hidden group flex items-center justify-between gap-2 sm:gap-3 {{ $filterStatus === 'disetujui' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-slate-200/90 hover:border-slate-300' }}">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">DISETUJUI</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-brand-600 mt-2 tracking-tight">{{ $this->stats['disetujui'] }}</p>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 border border-brand-100 transition-transform duration-200 group-hover:scale-105">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </button>

        <!-- Card 3: Diserahkan -->
        <button type="button" wire:click="$set('filterStatus', 'diserahkan')"
            class="text-left bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 shadow-card hover:shadow-lg cursor-pointer relative overflow-hidden group flex items-center justify-between gap-2 sm:gap-3 {{ $filterStatus === 'diserahkan' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/90 hover:border-slate-300' }}">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">DISERAHKAN</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-2 tracking-tight">{{ $this->stats['diserahkan'] }}</p>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 transition-transform duration-200 group-hover:scale-105">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </button>

        <!-- Card 4: Ditolak -->
        <button type="button" wire:click="$set('filterStatus', 'ditolak')"
            class="text-left bg-white rounded-3xl p-4 sm:p-5 border transition-all duration-200 shadow-card hover:shadow-lg cursor-pointer relative overflow-hidden group flex items-center justify-between gap-2 sm:gap-3 {{ $filterStatus === 'ditolak' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-200/90 hover:border-slate-300' }}">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">DITOLAK</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-rose-600 mt-2 tracking-tight">{{ $this->stats['ditolak'] }}</p>
            </div>
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 transition-transform duration-200 group-hover:scale-105">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
        </button>
    </div>

    {{-- Tabel Utama Container --}}
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden" wire:poll.30s>
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
            <div class="relative flex-1 min-w-0 sm:max-w-md">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="w-full bg-slate-50/60 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all"
                    placeholder="Cari No. Bon atau nama pemohon...">
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                @if($filterStatus)
                    <button type="button" wire:click="$set('filterStatus', '')"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition flex items-center gap-1 cursor-pointer">
                        <span class="material-symbols-outlined text-[15px]">close</span>
                        <span>Reset Filter</span>
                    </button>
                @endif
                <span class="text-xs text-slate-400 font-medium hidden sm:inline" wire:loading wire:target="search, filterStatus">Memuat data...</span>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs table-fixed">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th wire:click="sortBy('no_bon')" class="py-3.5 px-3 w-[12%] whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                <span>No. Bon</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'no_bon' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortColumn === 'no_bon' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'no_bon' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('user.name')" class="py-3.5 px-3 w-[16%] cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                <span>Pemohon</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'user.name' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortColumn === 'user.name' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'user.name' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('keperluan')" class="py-3.5 px-3 w-[20%] cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center gap-1">
                                <span>Keperluan</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'keperluan' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortColumn === 'keperluan' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'keperluan' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th class="py-3.5 px-3 w-[26%]">Barang Diminta</th>
                        <th wire:click="sortBy('created_at')" class="py-3.5 px-2 text-center w-[9%] whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center justify-center gap-1">
                                <span>Tgl.</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'created_at' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortColumn === 'created_at' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'created_at' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('status')" class="py-3.5 px-2 text-center w-[9%] whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div class="flex items-center justify-center gap-1">
                                <span>Status</span>
                                <span class="material-symbols-outlined text-[15px] {{ $sortColumn === 'status' ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
                                    {{ $sortColumn === 'status' && $sortDirection === 'asc' ? 'arrow_upward' : ($sortColumn === 'status' ? 'arrow_downward' : 'swap_vert') }}
                                </span>
                            </div>
                        </th>
                        <th class="py-3.5 px-2 text-center w-[8%] whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($this->requests as $req)
                        <tr class="hover:bg-slate-50/60 transition-colors" wire:key="req-{{ $req->id }}">
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <code class="font-mono font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded-md border border-brand-100 text-[11px] inline-block tracking-tight">
                                    {{ $req->no_bon }}
                                </code>
                            </td>
                            <td class="py-3.5 px-3">
                                <p class="font-bold text-slate-900 leading-snug break-words">{{ $req->user->name ?? '-' }}</p>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5 break-words">{{ $req->user->seksi ?? '-' }}</p>
                            </td>
                            <td class="py-3.5 px-3 text-slate-700 font-medium break-words whitespace-normal leading-relaxed">
                                {{ $req->keperluan }}
                            </td>
                            <td class="py-3.5 px-3 text-slate-600 font-medium break-words whitespace-normal leading-relaxed">
                                <ul class="space-y-1">
                                    @foreach($req->items as $item)
                                        <li class="break-words whitespace-normal leading-snug">
                                            {{ $item->nama_barang }} <span class="text-slate-400 font-normal">({{ $item->jumlah_diminta }} {{ $item->satuan }})</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3.5 px-2 whitespace-nowrap text-center text-slate-500 font-medium text-[11px]">
                                {{ $req->created_at->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-2 whitespace-nowrap text-center">
                                @php
                                    $statusPill = match($req->status) {
                                        'menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'disetujui' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'diserahkan' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-slate-100 text-slate-600 border-slate-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusPill }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-1 whitespace-nowrap text-center">
                                <div class="inline-flex items-center justify-center gap-0.5">
                                    @if($req->status === 'menunggu')
                                        <button type="button"
                                            @click="showSetujuiModal = true; $wire.openDetail({{ $req->id }})"
                                            class="p-1 text-emerald-600 hover:bg-emerald-50 rounded-lg border border-transparent hover:border-emerald-200 transition cursor-pointer shrink-0"
                                            title="Setujui Permintaan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                        <button type="button"
                                            @click="showTolakModal = true; $wire.openTolak({{ $req->id }})"
                                            class="p-1 text-rose-600 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer shrink-0"
                                            title="Tolak Permintaan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    @elseif($req->status === 'disetujui')
                                        <button type="button"
                                            @click="showSerahkanModal = true; $wire.openSerahkan({{ $req->id }})"
                                            class="p-1 text-emerald-600 hover:bg-emerald-50 rounded-lg border border-transparent hover:border-emerald-200 transition cursor-pointer shrink-0"
                                            title="Serahkan Barang">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                    @endif
                                    
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
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-[32px] text-slate-300">inbox</span>
                                    <p class="font-semibold text-slate-600">Tidak ada permintaan ditemukan.</p>
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
                <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-card flex flex-col gap-3" wire:key="req-mobile-{{ $req->id }}">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <div>
                            <code class="font-mono font-bold text-xs text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-100">
                                {{ $req->no_bon }}
                            </code>
                            <p class="text-[11px] text-slate-400 mt-1">{{ $req->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $statusPill }}">
                            {{ ucfirst($req->status) }}
                        </span>
                    </div>

                    <div class="text-xs space-y-2">
                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Pemohon & Seksi:</p>
                            <p class="font-bold text-slate-900 text-xs">{{ $req->user->name ?? '-' }}</p>
                            <p class="text-[11px] text-slate-400">{{ $req->user->seksi ?? '-' }}</p>
                        </div>

                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Keperluan:</p>
                            <p class="font-medium text-slate-800 leading-snug">{{ $req->keperluan }}</p>
                        </div>

                        <div class="bg-slate-50/70 p-2.5 rounded-xl border border-slate-100">
                            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-1">Barang Diminta:</p>
                            <ul class="space-y-0.5 text-slate-700">
                                @foreach($req->items as $item)
                                    <li>{{ $item->nama_barang }} <span class="text-slate-400 font-medium">({{ $item->jumlah_diminta }} {{ $item->satuan }})</span></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 flex-wrap">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @if($req->status === 'menunggu')
                                <button type="button" @click="showSetujuiModal = true; $wire.openDetail({{ $req->id }})"
                                    class="px-3 py-1.5 text-xs font-bold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 transition flex items-center gap-1 shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Setujui</span>
                                </button>
                                <button type="button" @click="showTolakModal = true; $wire.openTolak({{ $req->id }})"
                                    class="px-3 py-1.5 text-xs font-bold rounded-xl text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition flex items-center gap-1 shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    <span>Tolak</span>
                                </button>
                            @elseif($req->status === 'disetujui')
                                <button type="button" @click="showSerahkanModal = true; $wire.openSerahkan({{ $req->id }})"
                                    class="px-3 py-1.5 text-xs font-bold rounded-xl text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition flex items-center gap-1 shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    <span>Serahkan</span>
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-1 ml-auto">
                            <a href="{{ route('bona.print', $req->id) }}" target="_blank"
                                class="p-2 border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl transition cursor-pointer flex items-center justify-center shadow-2xs" title="Cetak Form">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-sm">
                    Tidak ada permintaan ditemukan.
                </div>
            @endforelse
        </div>

        @if($this->requests->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-white">
                {{ $this->requests->links() }}
            </div>
        @endif
    </div>

    {{-- ===== Modal Setujui ===== --}}
    <x-modal show="showSetujuiModal" maxWidth="max-w-xl" :dismissable="false">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Setujui Permintaan ATK</h2>
                    @if($this->selectedRequest)
                        <p class="text-xs text-brand-600 font-mono mt-0.5 font-bold">{{ $this->selectedRequest->no_bon }}</p>
                    @endif
                </div>
                <button type="button" @click="showSetujuiModal = false" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        @if($this->selectedRequest)
            @php $req = $this->selectedRequest; @endphp
            <div class="space-y-4 text-xs">
                <div class="rounded-2xl bg-blue-50/80 border border-blue-200/80 p-3.5 text-blue-700 flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-[18px] mt-0.5 shrink-0">info</span>
                    <p class="leading-relaxed">Atur jumlah barang yang akan diserahkan. Stok inventaris fisik akan terpotong otomatis sesuai jumlah di bawah ini.</p>
                </div>

                <div class="bg-slate-50/60 p-3 rounded-xl border border-slate-200">
                    <p class="text-slate-400 font-bold uppercase text-[10px] tracking-wider mb-0.5">Pemohon</p>
                    <p class="font-bold text-slate-900">{{ $req->user->name ?? '-' }} <span class="text-slate-400 font-normal">&bull; {{ $req->user->seksi ?? '' }}</span></p>
                </div>

                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-2.5">Barang</th>
                                <th class="px-4 py-2.5 text-center">Diminta</th>
                                <th class="px-4 py-2.5 text-center w-36">Akan Diberikan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($req->items as $item)
                                <tr wire:key="si-{{ $item->id }}">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($item->bonItem && $item->bonItem->image_path)
                                                <img src="{{ asset('storage/' . $item->bonItem->image_path) }}" class="h-8 w-8 rounded-lg object-cover border border-slate-200 shrink-0">
                                            @else
                                                <div class="h-8 w-8 rounded-lg bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200">
                                                    <span class="material-symbols-outlined text-slate-400 text-[15px]">inventory_2</span>
                                                </div>
                                            @endif
                                            <span class="font-bold text-slate-800">{{ $item->nama_barang }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-slate-600">
                                        {{ $item->jumlah_diminta }} {{ $item->satuan }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="number"
                                            wire:model="jumlahDiberikan.{{ $item->id }}"
                                            min="0"
                                            max="{{ $item->jumlah_diminta }}"
                                            class="w-24 mx-auto bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-center font-bold text-slate-800 focus:bg-white focus:border-brand-500 outline-none transition">
                                        @error("jumlahDiberikan.{$item->id}") <p class="text-[10px] text-rose-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showSetujuiModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="approve"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs cursor-pointer"
                    wire:loading.attr="disabled" wire:target="approve">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    <span>Konfirmasi Setujui</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- ===== Modal Tolak ===== --}}
    <x-modal show="showTolakModal" maxWidth="max-w-md" :dismissable="false">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Tolak Permintaan ATK</h2>
                <button type="button" @click="showTolakModal = false" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <div class="space-y-4 text-xs">
            <p class="text-slate-600 leading-relaxed">Berikan alasan penolakan. Alasan ini akan tercatat dan dapat dilihat oleh pegawai pemohon.</p>
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Penolakan <span class="text-rose-500">*</span></label>
                <textarea wire:model="catatanTolak" rows="3"
                    class="w-full p-3 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 outline-none transition resize-none"
                    placeholder="Contoh: Stok barang di gudang sedang habis atau tidak mencukupi..."></textarea>
                @error('catatanTolak') <span class="mt-1 text-[11px] text-rose-600 font-semibold block">{{ $message }}</span> @enderror
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showTolakModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="tolak"
                    class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md shadow-rose-600/20 transition text-xs flex items-center gap-1.5 cursor-pointer"
                    wire:loading.attr="disabled" wire:target="tolak">
                    <span class="material-symbols-outlined text-[16px]">block</span>
                    <span>Konfirmasi Tolak</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- ===== Modal Serahkan ===== --}}
    <x-modal show="showSerahkanModal" maxWidth="max-w-md" :dismissable="false">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Konfirmasi Penyerahan ATK</h2>
                <button type="button" @click="showSerahkanModal = false" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <div class="flex items-start gap-4">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">inventory</span>
            </div>
            <div>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Apakah Anda yakin seluruh barang permintaan ini telah diserahkan secara fisik kepada pegawai pemohon?
                </p>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showSerahkanModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="tandaiDiserahkan"
                    class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md shadow-emerald-600/20 transition text-xs flex items-center gap-1.5 cursor-pointer"
                    wire:loading.attr="disabled" wire:target="tandaiDiserahkan">
                    <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                    <span>Tandai Diserahkan</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- ===== Modal Export ===== --}}
    <x-modal show="showExportModal" maxWidth="max-w-sm" :dismissable="true">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-base font-bold text-slate-900">Export Laporan Bon ATK</h2>
                <button type="button" @click="showExportModal = false" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                    &times;
                </button>
            </div>
        </x-slot:header>

        <div class="space-y-4 text-xs">
            <p class="text-slate-600">Pilih periode bulan dan tahun data yang ingin diekspor ke format Excel.</p>
            <div class="grid grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bulan</label>
                    <select wire:model="exportMonth"
                        class="w-full px-3.5 py-2 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 outline-none transition">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tahun</label>
                    <select wire:model="exportYear"
                        class="w-full px-3.5 py-2 bg-slate-50/60 rounded-xl border border-slate-200 text-slate-800 text-xs font-medium focus:bg-white focus:border-brand-500 outline-none transition">
                        @foreach(range(date('Y') - 5, date('Y') + 1) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2.5 w-full">
                <button type="button" @click="showExportModal = false"
                    class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="export"
                    class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/25 transition text-xs flex items-center gap-1.5 cursor-pointer"
                    wire:loading.attr="disabled" wire:target="export">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span wire:loading.remove wire:target="export">Download Excel</span>
                    <span wire:loading wire:target="export">Memproses...</span>
                </button>
            </div>
        </x-slot:footer>
    </x-modal>

</div>
