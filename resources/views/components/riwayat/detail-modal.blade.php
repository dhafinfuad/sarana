{{--
    Modal Detail (Alpine, tanpa permintaan ke server). Parent x-data wajib punya:
      showDetailModal: false, detail: { title: '', badge: {bg, dot, label}, rows: [[label, value], ...] }
    Dibuka dari tombol: @click="detail = @js($payload); showDetailModal = true"
--}}
<x-modal show="showDetailModal" maxWidth="max-w-lg">
    <x-slot:header>
        <div class="flex justify-between items-center w-full">
            <h2 class="text-base font-bold text-slate-900" x-text="detail.title"></h2>
            <button type="button" @click="showDetailModal = false" aria-label="Tutup"
                class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">&times;</button>
        </div>
    </x-slot:header>

    <div class="space-y-3">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
            <span class="font-bold text-slate-500 uppercase tracking-wider">Status</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border" :class="detail.badge && detail.badge.bg">
                <span class="w-1.5 h-1.5 rounded-full" :class="detail.badge && detail.badge.dot"></span>
                <span x-text="detail.badge && detail.badge.label"></span>
            </span>
        </div>
        <template x-for="row in detail.rows" :key="row[0]">
            <div class="pb-3 border-b border-slate-200/80 last:border-0">
                <p class="font-bold text-slate-500 uppercase tracking-wider mb-1" x-text="row[0]"></p>
                <p class="text-sm text-slate-800 font-medium leading-relaxed break-words" x-text="row[1]"></p>
            </div>
        </template>
    </div>

    <x-slot:footer>
        <button type="button" @click="showDetailModal = false"
            class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">Tutup</button>
    </x-slot:footer>
</x-modal>
