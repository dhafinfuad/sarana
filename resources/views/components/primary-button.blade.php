{{--
    Komponen: <x-primary-button>
    Tombol aksi utama bergaya design-system dengan loading state & warna brand.
    Props:
      - $type     : type button (default: 'submit')
      - $loading  : boolean — tampilkan spinner (untuk Livewire wire:loading)
      - $disabled : boolean — nonaktifkan tombol
--}}
@props([
    'type'     => 'submit',
    'loading'  => false,
    'disabled' => false,
])

<button
    type="{{ $type }}"
    {{ ($disabled || $loading) ? 'disabled' : '' }}
    {{ $attributes->class([
        'w-full flex items-center justify-center gap-2',
        'py-2.5 px-4',
        'border border-transparent rounded-lg shadow-sm',
        'text-base font-semibold leading-6 text-white',
        'bg-[#004875]',
        'hover:bg-[#00609a]',
        'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#004875]',
        'active:scale-95',
        'transition-all duration-200',
        'disabled:opacity-60 disabled:cursor-not-allowed disabled:active:scale-100',
    ]) }}
>
    {{-- Spinner (tampil saat loading=true) --}}
    @if($loading)
        <svg class="animate-spin h-4 w-4 text-white flex-shrink-0"
             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    @endif

    {{ $slot }}
</button>
