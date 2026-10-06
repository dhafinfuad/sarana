{{--
    Komponen: <x-app-icon>
    Menampilkan logo/icon aplikasi SARANA dalam container biru navy.
    Props:
      - $size   : ukuran container (default: 'md' = w-16 h-16)
      - $class  : class tambahan pada wrapper
--}}
@props([
    'size'  => 'md',
    'class' => '',
])

@php
    $sizeClasses = match($size) {
        'sm'    => 'w-10 h-10 rounded-lg',
        'md'    => 'w-16 h-16 rounded-xl',
        'lg'    => 'w-20 h-20 rounded-2xl',
        default => 'w-16 h-16 rounded-xl',
    };

    $iconSizeClass = match($size) {
        'sm'    => 'text-xl',
        'md'    => 'text-3xl',
        'lg'    => 'text-4xl',
        default => 'text-3xl',
    };
@endphp

<div {{ $attributes->class([
    'bg-[#003152] flex items-center justify-center shadow-sm flex-shrink-0',
    $sizeClasses,
    $class,
]) }} aria-hidden="true">
    <span class="material-symbols-outlined text-white {{ $iconSizeClass }}"
          style="font-variation-settings: 'FILL' 1, 'wght' 600;">
        assured_workload
    </span>
</div>
