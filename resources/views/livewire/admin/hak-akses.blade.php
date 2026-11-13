<div x-data="{ showConfirm: false, confirmAction: '' }" @close-hak-akses-confirm.window="showConfirm = false">

    @if(!auth()->user()->canAccessAdminDashboard())
        <x-top-header title="Hak Akses" />
    @endif

    <div class="{{ auth()->user()->canAccessAdminDashboard() ? '' : 'py-6 pb-24 md:pb-8 max-w-7xl mx-auto flex-grow w-full px-4 sm:px-6 lg:px-8' }}">
        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-normal">Hak Akses</h1>
                <p class="text-slate-500 text-sm mt-1 leading-normal">Atur menu, halaman, dan tombol yang dapat dipakai setiap peran.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 self-start sm:self-auto">
                @if($this->dirtyCount > 0)
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        {{ $this->dirtyCount }} perubahan belum disimpan
                    </span>
                    <button type="button" wire:click="discard" class="text-xs font-semibold text-slate-500 hover:text-slate-700 hover:underline cursor-pointer">Batalkan</button>
                @endif
                <button type="button" @click="confirmAction = 'reset'; showConfirm = true"
                    class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs shadow-2xs hover:border-slate-300 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Kembalikan ke Bawaan</span>
                </button>
                <button type="button" @click="confirmAction = 'save'; showConfirm = true"
                    class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 hover:shadow-brand-600/35 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </div>

        @if (session()->has('success'))
            <div role="alert" class="mb-6 p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200/80 shadow-xs flex items-start gap-3">
                <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-bold text-emerald-900">Berhasil</p>
                    <p class="text-xs sm:text-sm text-emerald-700 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
            <!-- Cara Kerja -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-200/90 shadow-card">
                <h3 class="text-sm font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Cara Kerja</h3>
                <ul class="space-y-2.5 text-xs text-slate-600 leading-relaxed">
                    <li class="flex gap-2"><span class="text-brand-600 font-bold">1.</span><span><b class="text-slate-800">Peran dihitung otomatis</b> dari jabatan, seksi, dan PLH/PLT yang aktif. Satu orang bisa punya beberapa peran; ia boleh memakai fitur bila <b class="text-slate-800">salah satu</b> perannya dicentang.</span></li>
                    <li class="flex gap-2"><span class="text-brand-600 font-bold">2.</span><span><b class="text-slate-800">Pegawai</b> = semua pengguna kecuali Kepala Kantor definitif. Untuk mencabut akses sebuah kelompok, lepas centangnya dari semua peran yang mereka pegang.</span></li>
                    <li class="flex gap-2"><span class="text-brand-600 font-bold">3.</span><span><b class="text-slate-800">Administrator</b> selalu mendapat semua akses, dan halaman Hak Akses hanya untuk Administrator.</span></li>
                    <li class="flex gap-2"><span class="text-brand-600 font-bold">4.</span><span>Centang mengatur boleh/tidaknya memakai menu, halaman, atau tombol. <b class="text-slate-800">Cakupan data tetap</b> (mis. Kepala Seksi hanya menyetujui bawahan seksinya).</span></li>
                    <li class="flex gap-2 items-center"><span class="text-brand-600 font-bold">5.</span><span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> menandai pengaturan yang berbeda dari bawaan.</span></li>
                </ul>
            </div>

            <!-- Cek Pengguna -->
            <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-slate-200/90 shadow-card">
                <h3 class="text-sm font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Cek Pengguna</h3>
                @if($this->checked)
                    @php $c = $this->checked; @endphp
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900 truncate">{{ $c['user']->name }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5 truncate">NIP {{ $c['user']->nip_pendek }} &bull; {{ $c['user']->jabatan ?: '-' }} &bull; {{ $c['user']->seksi ?: '-' }}</p>
                        </div>
                        <button type="button" wire:click="clearUser" class="text-xs font-semibold text-brand-600 hover:underline cursor-pointer shrink-0">Ganti</button>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        @foreach($c['roles'] as $role)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200">{{ $role }}</span>
                        @endforeach
                    </div>
                    <div class="mt-4 space-y-2 max-h-56 overflow-y-auto pr-1">
                        @forelse($c['access'] as $module => $labels)
                            <div class="text-xs">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $module }}</p>
                                <p class="text-slate-700 mt-0.5 leading-relaxed">{{ implode(', ', $labels) }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">Tidak punya akses ke fitur apa pun.</p>
                        @endforelse
                    </div>
                    <p class="text-[10px] text-slate-400 mt-3">Berdasarkan pengaturan yang sudah disimpan.</p>
                @else
                    <p class="text-xs text-slate-500 mb-3">Lihat peran dan akses efektif seorang pegawai.</p>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="userSearch" placeholder="Cari nama atau NIP pegawai..."
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 hover:bg-slate-50/80 focus:bg-white text-xs font-medium rounded-xl border border-slate-200 focus:border-brand-600 outline-none transition" />
                    </div>
                    @if($this->userOptions->isNotEmpty())
                        <div class="mt-2 border border-slate-100 rounded-2xl divide-y divide-slate-100 overflow-hidden">
                            @foreach($this->userOptions as $opt)
                                <button type="button" wire:click="selectUser({{ $opt->id }})" class="w-full text-left px-3.5 py-2.5 hover:bg-brand-50/60 transition cursor-pointer">
                                    <p class="text-xs font-bold text-slate-800">{{ $opt->name }}</p>
                                    <p class="text-[10px] text-slate-400">NIP {{ $opt->nip_pendek }} &bull; {{ $opt->jabatan ?: '-' }}</p>
                                </button>
                            @endforeach
                        </div>
                    @elseif(mb_strlen(trim($userSearch)) >= 2)
                        <p class="text-xs text-slate-400 mt-2">Pegawai tidak ditemukan.</p>
                    @endif
                @endif
            </div>
        </div>

        <!-- Matriks per Modul -->
        <div class="space-y-6">
            @foreach($modules as $module => $items)
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden" wire:key="modul-{{ \Illuminate\Support\Str::slug($module) }}">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900">{{ $module }}</h3>
                        <span class="text-[11px] text-slate-400">{{ count($items) }} hak akses</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[860px]">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                                    <th class="py-3 px-6 w-[32%]">Hak Akses</th>
                                    @foreach($roles as $role => $label)
                                        <th class="py-3 px-2 text-center leading-tight">{{ $label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($items as $key => $meta)
                                    @php
                                        $slot = \App\Livewire\Admin\HakAkses::slot($key);
                                        $locked = \App\Support\HakAkses::isLocked($key);
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 transition-colors" wire:key="row-{{ $slot }}">
                                        <td class="py-3.5 px-6">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-bold text-slate-900">{{ $meta['label'] }}</p>
                                                <span @class([
                                                    'px-1.5 py-0.5 rounded text-[10px] font-bold border',
                                                    'bg-brand-50 text-brand-700 border-brand-100' => $meta['type'] !== 'Tombol',
                                                    'bg-amber-50 text-amber-700 border-amber-100' => $meta['type'] === 'Tombol',
                                                ])>{{ $meta['type'] }}</span>
                                                @if($locked)
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Dikunci</span>
                                                @endif
                                            </div>
                                            <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">{{ $meta['desc'] }}</p>
                                        </td>
                                        @foreach($roles as $role => $label)
                                            @php
                                                $disabled = $locked || $role === \App\Support\HakAkses::ADMIN;
                                                $value = (bool) ($matrix[$slot][$role] ?? false);
                                                $changed = !$disabled && $value !== \App\Support\HakAkses::defaultAllows($role, $key);
                                            @endphp
                                            <td class="py-3.5 px-2 text-center">
                                                <label class="inline-flex flex-col items-center gap-1 {{ $disabled ? 'cursor-not-allowed' : 'cursor-pointer' }}"
                                                    title="{{ $label }} — {{ $meta['label'] }}{{ $disabled ? ' (dikunci)' : '' }}{{ $changed ? ' (berbeda dari bawaan)' : '' }}">
                                                    <input type="checkbox" wire:model.live="matrix.{{ $slot }}.{{ $role }}" @disabled($disabled)
                                                        class="w-4 h-4 rounded accent-brand-600 {{ $disabled ? 'opacity-50' : 'cursor-pointer' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $changed ? 'bg-amber-500' : 'bg-transparent' }}"></span>
                                                </label>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <x-modal show="showConfirm" maxWidth="max-w-md">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h3 class="text-base font-bold text-slate-900" x-text="confirmAction === 'reset' ? 'Kembalikan ke Bawaan' : 'Simpan Hak Akses'"></h3>
                <button type="button" @click="showConfirm = false" class="text-slate-400 hover:text-slate-700 text-xl leading-none cursor-pointer">&times;</button>
            </div>
        </x-slot:header>

        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-200">
                <span class="material-symbols-outlined text-[24px]">warning</span>
            </div>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed"
                x-text="confirmAction === 'reset'
                    ? 'Semua pengaturan hak akses akan dikembalikan ke bawaan. Perubahan langsung berlaku untuk semua pengguna. Lanjutkan?'
                    : 'Perubahan hak akses langsung berlaku untuk semua pengguna. Pengguna yang kehilangan akses tidak bisa lagi membuka menu, halaman, atau tombol tersebut. Lanjutkan?'"></p>
        </div>

        <x-slot:footer>
            <button type="button" @click="showConfirm = false"
                class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">Batal</button>
            <button type="button" x-show="confirmAction === 'save'" wire:click="save"
                class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-md shadow-brand-600/20 transition-colors cursor-pointer flex items-center gap-1.5">
                <span>Ya, Simpan</span>
                <span wire:loading wire:target="save" class="material-symbols-outlined animate-spin text-[14px]">refresh</span>
            </button>
            <button type="button" x-show="confirmAction === 'reset'" wire:click="resetDefaults"
                class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/20 transition-colors cursor-pointer flex items-center gap-1.5">
                <span>Ya, Kembalikan</span>
                <span wire:loading wire:target="resetDefaults" class="material-symbols-outlined animate-spin text-[14px]">refresh</span>
            </button>
        </x-slot:footer>
    </x-modal>
</div>
