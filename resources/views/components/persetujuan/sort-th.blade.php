{{-- Header kolom yang bisa diurutkan, gaya /admin/dashboard. $sort/$dir = sortField/sortDirection komponen induk. --}}
@props(['field', 'sort', 'dir', 'width' => 'w-[10%]', 'center' => false])

<th wire:click="sortBy('{{ $field }}')" class="{{ $width }} py-3 px-3 cursor-pointer hover:bg-slate-100 transition-colors group {{ $center ? 'text-center' : '' }}">
    <div class="flex items-center gap-1 {{ $center ? 'justify-center' : '' }}">
        <span>{{ $slot }}</span>
        <span class="material-symbols-outlined text-[15px] {{ $sort === $field ? 'text-brand-600' : 'opacity-30 group-hover:opacity-100' }}">
            {{ $sort === $field && $dir === 'asc' ? 'arrow_upward' : ($sort === $field ? 'arrow_downward' : 'swap_vert') }}
        </span>
    </div>
</th>
