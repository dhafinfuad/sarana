<div wire:poll.10s="updateCount">
    <div x-data="{ open: false, tab: 'all' }" class="relative" @click.outside="open = false" wire:ignore.self>
        
        <!-- Bell Button Trigger -->
        <button 
            @click.stop="open = !open" 
            type="button" 
            class="relative p-2 rounded-2xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none cursor-pointer"
            title="Pemberitahuan Sistem"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>

            <!-- Notification Badge -->
            @if($pendingCount > 0)
                <span class="absolute top-0.5 right-0.5 px-1.5 py-0.5 text-[9px] font-black bg-rose-600 text-white rounded-full ring-2 ring-white shadow-xs">
                    {{ $pendingCount > 99 ? '99+' : $pendingCount }}
                </span>
            @endif
        </button>

        <!-- Pop-over Panel -->
        <div 
            x-show="open" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            style="display: none;" 
            class="absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-3xl shadow-2xl border border-slate-200/90 z-50 overflow-hidden"
        >
            <!-- Popover Header -->
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                <div class="flex items-center gap-2">
                    <h3 class="font-extrabold text-sm text-slate-900">Notifikasi</h3>
                    @if($pendingCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $pendingCount }} Baru
                        </span>
                    @endif
                </div>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer" title="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Filter Tabs Row -->
            <div class="px-4 py-2.5 border-b border-slate-100 flex items-center gap-1.5 bg-white overflow-x-auto text-xs">
                <button type="button" @click="tab = 'all'"
                    :class="tab === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                    class="px-3 py-1 rounded-full font-bold transition cursor-pointer shrink-0">
                    Semua
                </button>
                @if($peminjamanCount > 0)
                    <button type="button" @click="tab = 'sarana'"
                        :class="tab === 'sarana' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                        class="px-3 py-1 rounded-full font-bold transition cursor-pointer shrink-0">
                        Sarana ({{ $peminjamanCount }})
                    </button>
                @endif
                @if($permisiCount > 0)
                    <button type="button" @click="tab = 'permisi'"
                        :class="tab === 'permisi' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                        class="px-3 py-1 rounded-full font-bold transition cursor-pointer shrink-0">
                        Izin ({{ $permisiCount }})
                    </button>
                @endif
                @if($bonAtkCount > 0)
                    <button type="button" @click="tab = 'bona'"
                        :class="tab === 'bona' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                        class="px-3 py-1 rounded-full font-bold transition cursor-pointer shrink-0">
                        Bon ATK ({{ $bonAtkCount }})
                    </button>
                @endif
            </div>

            <!-- List Container: Menjabarkan Seluruh Notifikasi -->
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse($notifications as $notif)
                    <a wire:navigate href="{{ $notif['url'] }}"
                        x-show="tab === 'all' || tab === '{{ $notif['type'] }}'"
                        @click="open = false"
                        class="flex items-start gap-3.5 p-4 hover:bg-slate-50/80 transition group {{ $loop->first ? 'bg-slate-50/30' : '' }}">
                        
                        <!-- Module Icon -->
                        <div class="w-9 h-9 rounded-2xl flex items-center justify-center shrink-0 border mt-0.5 {{ 
                            $notif['type'] === 'sarana' ? 'bg-blue-50 text-blue-600 border-blue-100' : 
                            ($notif['type'] === 'permisi' ? 'bg-purple-50 text-purple-600 border-purple-100' : 
                            'bg-amber-50 text-amber-600 border-amber-100') 
                        }}">
                            @if($notif['type'] === 'sarana')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10"></path>
                                </svg>
                            @elseif($notif['type'] === 'permisi')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0 pr-1">
                            <div class="flex items-center justify-between gap-2 mb-0.5">
                                <p class="text-xs font-bold text-slate-900 group-hover:text-brand-600 truncate">
                                    {{ $notif['title'] }}
                                </p>
                                <span class="px-1.5 py-0.2 text-[9px] font-bold rounded-md shrink-0 {{ $notif['badge_color'] }}">
                                    {{ $notif['badge'] }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                                {{ $notif['description'] }}
                            </p>
                            <div class="flex items-center gap-1.5 mt-1 text-[10px] text-slate-400">
                                <span>{{ $notif['created_at']->diffForHumans(short: true) }}</span>
                                <span>•</span>
                                <span class="font-semibold {{ 
                                    $notif['type'] === 'sarana' ? 'text-blue-600' : 
                                    ($notif['type'] === 'permisi' ? 'text-purple-600' : 'text-amber-600') 
                                }}">{{ $notif['module'] }}</span>
                            </div>
                        </div>

                        <!-- Blue Unread Dot -->
                        <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0 mt-2"></span>
                    </a>
                @empty
                    <div class="px-5 py-8 text-center text-slate-400 flex flex-col items-center justify-center gap-2">
                        <div class="w-10 h-10 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center border border-slate-100">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Tidak ada notifikasi baru</p>
                        <p class="text-[11px] text-slate-400">Semua permohonan telah diproses</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
