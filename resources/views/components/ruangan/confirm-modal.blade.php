{{--
    Modal konfirmasi aksi (Alpine). Dipakai di halaman modul Ruangan.
    Membutuhkan state pada x-data induk: showConfirmModal, confirmAction, confirmTitle,
    confirmMessage, dan method executeAction(). Tidak bisa ditutup dengan klik backdrop.
    Aksi positif (hijau): approve, activate. Aksi negatif (merah): reject, cancel, deactivate.
--}}
<x-modal show="showConfirmModal" maxWidth="max-w-md">
    <x-slot:header>
        <div class="flex justify-between items-center w-full">
            <h2 class="text-base font-bold text-slate-900" x-text="confirmTitle"></h2>
            <button type="button" @click="showConfirmModal = false" aria-label="Tutup"
                class="text-slate-400 hover:text-slate-700 p-1 text-xl leading-none transition-colors cursor-pointer">
                &times;
            </button>
        </div>
    </x-slot:header>

    <div class="flex items-start gap-4">
        <div class="flex-shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center"
            :class="['approve', 'activate'].includes(confirmAction) ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200'">
            <span class="material-symbols-outlined text-[24px]"
                x-text="['approve', 'activate'].includes(confirmAction) ? 'check_circle' : 'warning'"></span>
        </div>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed" x-text="confirmMessage"></p>
    </div>

    <x-slot:footer>
        <button type="button" @click="showConfirmModal = false"
            class="px-5 py-2 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition text-xs cursor-pointer">
            Batal
        </button>
        <button type="button" x-show="['approve', 'activate'].includes(confirmAction)" @click="executeAction()"
            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20 transition-colors cursor-pointer">
            Ya, Lanjutkan
        </button>
        <button type="button" x-show="!['approve', 'activate'].includes(confirmAction)" @click="executeAction()"
            class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-600/20 transition-colors cursor-pointer">
            Ya, Lanjutkan
        </button>
    </x-slot:footer>
</x-modal>
