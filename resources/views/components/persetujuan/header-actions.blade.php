{{--
    Tombol aksi header halaman Persetujuan, sama dengan tombol "Pengajuan" dan "Export" di /admin/dashboard.
    $createAction = ekspresi Alpine yang membuka modal form (mis. "showApplicationModal = true").
    Tombol Export membuka showExportModal (lihat x-persetujuan.export-modal).
--}}
@props(['create' => false, 'createLabel' => 'Pengajuan', 'createAction' => '', 'export' => false])

@if($create || $export)
    <div class="flex items-center gap-2.5 self-start sm:self-auto">
        @if($create)
            <button @click="{{ $createAction }}" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 hover:shadow-brand-600/35 transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>{{ $createLabel }}</span>
            </button>
        @endif
        @if($export)
            <button @click="showExportModal = true" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs shadow-2xs hover:border-slate-300 transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Export</span>
            </button>
        @endif
    </div>
@endif
