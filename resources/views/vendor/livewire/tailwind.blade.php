@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center justify-between font-sans">
            {{-- Mobile Pagination (sm:hidden) --}}
            <div class="flex items-center justify-between flex-1 sm:hidden">
                <div>
                    @if ($paginator->onFirstPage())
                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-400 bg-slate-50 border border-slate-200/80 rounded-xl cursor-not-allowed select-none">
                            &laquo; {!! __('pagination.previous') !!}
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-brand-600 transition shadow-2xs active:scale-95 cursor-pointer">
                            &laquo; {!! __('pagination.previous') !!}
                        </button>
                    @endif
                </div>

                <div class="text-xs text-slate-500 font-medium">
                    <span class="font-bold text-slate-800">{{ $paginator->currentPage() }}</span> / <span class="font-bold text-slate-800">{{ $paginator->lastPage() }}</span>
                </div>

                <div>
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-brand-600 transition shadow-2xs active:scale-95 cursor-pointer">
                            {!! __('pagination.next') !!} &raquo;
                        </button>
                    @else
                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-400 bg-slate-50 border border-slate-200/80 rounded-xl cursor-not-allowed select-none">
                            {!! __('pagination.next') !!} &raquo;
                        </span>
                    @endif
                </div>
            </div>

            {{-- Desktop / Tablet Pagination (hidden sm:flex) --}}
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs text-slate-500 font-medium">
                        <span>Menampilkan</span>
                        <span class="font-bold text-slate-900">{{ $paginator->firstItem() }}</span>
                        <span>sampai</span>
                        <span class="font-bold text-slate-900">{{ $paginator->lastItem() }}</span>
                        <span>dari</span>
                        <span class="font-bold text-slate-900">{{ $paginator->total() }}</span>
                        <span>data</span>
                    </p>
                </div>

                <div>
                    <div class="flex items-center gap-1 select-none">
                        {{-- Previous Page Button --}}
                        @if ($paginator->onFirstPage())
                            <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="w-8 h-8 flex items-center justify-center rounded-xl border border-slate-200/70 bg-slate-50/80 text-slate-300 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </span>
                        @else
                            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="w-8 h-8 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-brand-600 hover:bg-brand-50/60 hover:border-brand-200 transition shadow-2xs active:scale-95 cursor-pointer" aria-label="{{ __('pagination.previous') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate-400">
                                    {{ $element }}
                                </span>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                        @if ($page == $paginator->currentPage())
                                            <span aria-current="page" class="w-8 h-8 flex items-center justify-center rounded-xl bg-brand-600 text-white font-bold text-xs shadow-xs border border-brand-600 cursor-default">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="w-8 h-8 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:text-brand-600 hover:bg-brand-50/60 hover:border-brand-200 font-semibold text-xs transition shadow-2xs active:scale-95 cursor-pointer" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                                {{ $page }}
                                            </button>
                                        @endif
                                    </span>
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Next Page Button --}}
                        @if ($paginator->hasMorePages())
                            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="w-8 h-8 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-brand-600 hover:bg-brand-50/60 hover:border-brand-200 transition shadow-2xs active:scale-95 cursor-pointer" aria-label="{{ __('pagination.next') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @else
                            <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="w-8 h-8 flex items-center justify-center rounded-xl border border-slate-200/70 bg-slate-50/80 text-slate-300 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </nav>
    @endif
</div>
