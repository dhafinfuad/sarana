{{--
    Reusable Modal Component: <x-modal>
    Sesuai standar redesain modern sarana_redesain.html
--}}
@props([
    'show'        => 'open',
    'maxWidth'    => 'max-w-lg',
    'dismissable' => false,
    'fullWidth'   => true,
])

<div x-show="{{ $show }}" style="display:none;"
    data-modal-container
    class="app-modal-container fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-xs flex justify-center items-center p-4"
    wire:ignore.self
    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    <div {{ $attributes->merge(['class' => "bg-white rounded-3xl shadow-2xl border border-slate-100 " . ($fullWidth ? 'w-full ' : '') . "{$maxWidth} flex flex-col overflow-hidden max-h-[90vh] transform"]) }}
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">

        {{-- Header --}}
        @if(isset($header))
            <div class="px-6 sm:px-7 py-4 border-b border-slate-100 flex justify-between items-center bg-white flex-shrink-0 w-full">
                <div class="text-base font-bold text-slate-900 w-full">
                    {{ $header }}
                </div>
            </div>
        @endif

        {{-- Body --}}
        <div class="p-6 sm:p-7 bg-white overflow-y-auto text-xs space-y-4">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @if(isset($footer))
            <div class="px-6 sm:px-7 py-4 border-t border-slate-100 bg-white flex justify-end gap-2.5 flex-shrink-0">
                {{ $footer }}
            </div>
        @endif

    </div>
</div>
