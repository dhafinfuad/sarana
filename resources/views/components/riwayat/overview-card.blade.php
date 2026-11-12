{{-- Kartu "Overview Status" kolom kiri, sama dengan halaman /peminjaman. $rows: [['label' =>, 'dot' => kelas warna, 'value' =>], ...] --}}
@props(['title', 'rows'])

<div wire:poll.10s class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-card">
    <h3 class="text-sm font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">{{ $title }}</h3>
    <div class="space-y-3.5 text-xs">
        @foreach($rows as $row)
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2 font-medium text-slate-600">
                    <span class="w-2.5 h-2.5 rounded-full {{ $row['dot'] }}"></span> {{ $row['label'] }}
                </span>
                <strong class="font-bold text-slate-800">{{ $row['value'] }}</strong>
            </div>
        @endforeach
    </div>
</div>
