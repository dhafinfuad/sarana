{{--
    Modal "Export Laporan ..." sama dengan modal Export di /admin/dashboard: Periode Mulai dan Periode Selesai
    (bulan + tahun), tombol Unduh Laporan. State periode lokal (Alpine). Parent x-data wajib punya showExportModal.
--}}
@props(['title', 'url'])

<div x-data="{ sm: '{{ now()->month }}', sy: '{{ now()->year }}', em: '{{ now()->month }}', ey: '{{ now()->year }}' }">
    <x-modal show="showExportModal" maxWidth="max-w-md">
        <x-slot:header>
            <div class="flex justify-between items-center w-full">
                <h3 class="text-base font-bold text-slate-900">{{ $title }}</h3>
                <button type="button" @click="showExportModal = false" class="text-slate-400 hover:text-slate-700 text-xl leading-none cursor-pointer">&times;</button>
            </div>
        </x-slot:header>

        <p class="text-xs text-slate-500 mb-4">Pilih rentang periode laporan yang ingin diunduh (Format: Excel .xlsx).</p>

        <div class="space-y-4 text-xs">
            @foreach(['Periode Mulai' => ['sm', 'sy'], 'Periode Selesai' => ['em', 'ey']] as $label => [$month, $year])
                <div>
                    <p class="font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-2">{{ $label }}</p>
                    <div class="grid grid-cols-2 gap-3">
                        <select x-model="{{ $month }}" class="w-full px-3 py-2 bg-slate-50 rounded-xl border border-slate-200 outline-none text-xs">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                        <select x-model="{{ $year }}" class="w-full px-3 py-2 bg-slate-50 rounded-xl border border-slate-200 outline-none text-xs">
                            @foreach(range(now()->year, now()->year - 5) as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endforeach
        </div>

        <x-slot:footer>
            <button @click="showExportModal = false" type="button"
                class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                Batal
            </button>
            <a :href="'{{ $url }}?start_month=' + sm + '&start_year=' + sy + '&end_month=' + em + '&end_year=' + ey"
                @click="showExportModal = false"
                class="px-5 py-2.5 rounded-xl text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white shadow-md shadow-brand-600/25 flex items-center gap-1.5 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Unduh Laporan</span>
            </a>
        </x-slot:footer>
    </x-modal>
</div>
