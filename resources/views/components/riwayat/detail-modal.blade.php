{{--
    Modal Detail riwayat (Alpine, tanpa permintaan ke server). Markup mengikuti modal "Detail Peminjaman" di /peminjaman.
    Parent x-data wajib punya: showDetailModal: false, detail: {} (hasil App\Support\RiwayatDetail::make).
    Dibuka dari tombol: @click="detail = @js($payload); showDetailModal = true"
--}}
<x-modal show="showDetailModal" maxWidth="max-w-lg">
    <x-slot:header>
        <div class="flex justify-between items-center w-full">
            <h3 class="text-base font-bold text-slate-900" x-text="detail.title"></h3>
            <button @click="showDetailModal = false" type="button" class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer hover:bg-slate-100 rounded-lg" title="Tutup">&times;</button>
        </div>
    </x-slot:header>

    <div class="bg-slate-50 rounded-2xl border border-slate-200/80 p-5 text-xs space-y-3.5">
        <div class="grid grid-cols-2 gap-3 pb-3 border-b border-slate-200/80">
            <div>
                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">No. Request</span>
                <span class="block text-xs font-bold text-slate-800 mt-1" x-text="detail.code"></span>
            </div>
            <div>
                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold mt-1"
                    :class="detail.status && detail.status.class" x-text="detail.status && detail.status.label"></span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 pb-3 border-b border-slate-200/80">
            <div>
                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Mulai</span>
                <span class="block font-medium text-slate-800 mt-0.5" x-text="detail.mulai"></span>
            </div>
            <div>
                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Selesai</span>
                <span class="block font-medium text-slate-800 mt-0.5" x-text="detail.selesai"></span>
            </div>
        </div>

        <template x-for="(pair, i) in (detail.pairs || [])" :key="i">
            <div class="grid grid-cols-2 gap-3 pb-3 border-b border-slate-200/80">
                <template x-for="(cell, j) in pair" :key="j">
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider" x-text="cell.label"></span>
                        <template x-if="cell.style === 'chip' || cell.style === 'mono'">
                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 mt-0.5"
                                :class="cell.style === 'mono' ? 'font-mono' : ''" x-text="cell.value"></span>
                        </template>
                        <template x-if="cell.style === 'bold'">
                            <span class="block font-bold text-slate-800 mt-0.5" x-text="cell.value"></span>
                        </template>
                        <template x-if="cell.style === 'normal'">
                            <span class="block font-medium text-slate-800 mt-0.5" x-text="cell.value"></span>
                        </template>
                    </div>
                </template>
            </div>
        </template>

        <div>
            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider" x-text="detail.note && detail.note.label"></span>
            <p class="font-semibold text-slate-800 mt-0.5" x-show="detail.note && detail.note.heading" x-text="detail.note && detail.note.heading"></p>
            <p class="text-slate-600 mt-1 leading-relaxed" x-text="detail.note && detail.note.text"></p>
        </div>
    </div>

    <x-slot:footer>
        <button @click="showDetailModal = false" type="button"
            class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
            Tutup
        </button>
    </x-slot:footer>
</x-modal>
