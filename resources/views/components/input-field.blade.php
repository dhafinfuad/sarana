{{--
    Komponen: <x-input-field>
    Input field seragam bergaya design-system dengan ikon kiri opsional.
    Props:
      - $id          : id & name input (wajib)
      - $label       : teks label (wajib)
      - $type        : type input (default: 'text')
      - $placeholder : placeholder teks
      - $icon        : nama Material Symbols untuk ikon kiri (opsional)
      - $required    : boolean required (default: false)
      - $value       : nilai awal (old() akan diprioritaskan)
      - $error       : pesan error validasi (opsional)
--}}
@props([
    'id'          => '',
    'label'       => '',
    'type'        => 'text',
    'placeholder' => '',
    'icon'        => null,
    'required'    => false,
    'value'       => '',
    'error'       => null,
])

@php
    $hasError    = ! empty($error);
    $borderClass = $hasError
        ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]'
        : 'border-[#c1c7d0] focus:border-[#004875] focus:ring-[#004875]';
    $paddingLeft = $icon ? 'pl-10' : 'pl-3';
@endphp

<div class="space-y-1">
    {{-- Label --}}
    <label for="{{ $id }}"
           class="block text-sm font-medium text-[#191c1f] leading-5">
        {{ $label }}
        @if($required)
            <span class="text-[#ba1a1a] ml-0.5" aria-hidden="true">*</span>
        @endif
    </label>

    {{-- Input Wrapper --}}
    <div class="relative">
        {{-- Ikon Kiri --}}
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                 aria-hidden="true">
                <span class="material-symbols-outlined text-[#c1c7d0] text-xl select-none">
                    {{ $icon }}
                </span>
            </div>
        @endif

        {{-- Input Element --}}
        <input
            id="{{ $id }}"
            name="{{ $id }}"
            type="{{ $type }}"
            placeholder="{{ $placeholder }}"
            value="{{ old($id, $value) }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->except(['class']) }}
            class="w-full bg-surface-container-lowest border border-outline-variant rounded-md px-4 py-2 text-[13px] text-on-surface focus:ring-1 focus:ring-[#2d7dbe] outline-none transition-all {{ $attributes->get('class') }}"
            aria-describedby="{{ $hasError ? $id.'-error' : '' }}"
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
        >

        {{-- Slot untuk elemen kanan (toggle password dll.) --}}
        {{ $right ?? '' }}
    </div>

    {{-- Pesan Error --}}
    @if($hasError)
        <p id="{{ $id }}-error"
           role="alert"
           class="flex items-center gap-1.5 text-xs font-semibold text-rose-600 mt-1.5">
            <span class="material-symbols-outlined text-[16px] text-rose-500" aria-hidden="true">error</span>
            <span>{{ $error }}</span>
        </p>
    @endif
</div>
