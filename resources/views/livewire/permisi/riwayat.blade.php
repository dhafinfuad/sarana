<div x-data="{ showDetailModal: false, detail: { title: '', badge: {}, rows: [] } }">

    @if(!auth()->user()->canAccessAdminDashboard())
        <x-top-header title="Riwayat Permohonan Izin" />
    @endif

    <div class="{{ auth()->user()->canAccessAdminDashboard() ? '' : 'py-6 pb-24 md:pb-8 max-w-7xl mx-auto flex-grow w-full px-4 sm:px-6 lg:px-8' }}">
        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <a href="{{ route('permisi.permohonan') }}" wire:navigate.hover class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:text-brand-700 transition mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    <span>Kembali ke Dasbor</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">Riwayat Permohonan Izin</h1>
                <p class="text-slate-500 text-sm mt-1.5">Seluruh riwayat permohonan izin keluar kantor Anda.</p>
            </div>
            <button type="button" wire:click="filterThisMonth"
                class="self-start sm:self-auto px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
                Bulan Ini
            </button>
        </div>

        <x-riwayat.filter-bar placeholder="Cari Kategori / Jenis / Keperluan..." :statuses="['pending' => 'Pending', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan']" />

        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            @foreach(['kategori' => 'Kategori / Jenis', 'keperluan' => 'Keperluan', 'tanggal_mulai' => 'Mulai', 'tanggal_selesai' => 'Selesai', 'status' => 'Status'] as $field => $label)
                                <th wire:click="sortBy('{{ $field }}')" class="py-3.5 px-4 first:px-6 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors {{ $field === 'status' ? 'text-center' : '' }}">
                                    {{ $label }}
                                    @if($sortField === $field)<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                                </th>
                            @endforeach
                            <th class="py-3.5 px-6 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($this->permisis as $row)
                            @php
                                $badge = \App\Support\StatusBadge::for($row->status);
                                $payload = [
                                    'title' => $row->kategori . ' - ' . $row->jenis,
                                    'badge' => $badge,
                                    'rows' => array_values(array_filter([
                                        ['Keperluan', $row->keperluan],
                                        ['Waktu Mulai', $row->tanggal_mulai->translatedFormat('d M Y H:i')],
                                        ['Waktu Selesai', $row->tanggal_selesai->translatedFormat('d M Y H:i')],
                                        ['Diajukan', $row->created_at->translatedFormat('d M Y H:i')],
                                        $row->processor ? ['Diproses Oleh', $row->processor->name] : null,
                                    ])),
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors" wire:key="riwayat-{{ $row->id }}">
                                <td class="py-4 px-6"><p class="font-bold text-slate-900">{{ $row->kategori }}</p><p class="text-[10px] text-slate-400 mt-0.5">{{ $row->jenis }}</p></td>
                                <td class="py-4 px-4 text-slate-700">{{ $row->keperluan }}</td>
                                <td class="py-4 px-4 whitespace-nowrap text-slate-500 font-medium">{{ $row->tanggal_mulai->translatedFormat('d M Y H:i') }}</td>
                                <td class="py-4 px-4 whitespace-nowrap text-slate-500 font-medium">{{ $row->tanggal_selesai->translatedFormat('d M Y H:i') }}</td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge['bg'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>{{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center whitespace-nowrap">
                                    <button type="button" @click="detail = @js($payload); showDetailModal = true"
                                        class="px-3 py-1 rounded-xl text-[11px] font-bold border border-brand-200 text-brand-700 bg-brand-50/50 hover:bg-brand-100 transition cursor-pointer"
                                        title="Klik untuk melihat detail">Detail</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <span class="material-symbols-outlined text-[48px] text-slate-300 mb-2 block">description</span>
                                    <p class="text-slate-500 font-semibold text-sm">Tidak ada riwayat permohonan ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($this->permisis->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $this->permisis->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
    </div>

    <x-riwayat.detail-modal />
</div>
