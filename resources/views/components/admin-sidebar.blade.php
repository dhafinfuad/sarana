<!-- SideNavBar (Desktop) -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="hidden lg:flex flex-col fixed left-0 top-0 h-full py-6 px-4 bg-surface-glass font-label-md text-label-md w-64 backdrop-blur-md border-r border-border-glass shadow-md z-40 pt-20 transition-transform duration-300">

    <nav class="flex-1 flex flex-col gap-1">
        <div
            x-data="{ open: {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.fleet') ? 'true' : 'false' }} }">
            <!-- MENU PARENT -->
            <button type="button" @click="open = !open" @class([
                'w-full flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300 group',
                'text-primary font-bold' => request()->routeIs('admin.dashboard') || request()->routeIs('admin.fleet'),
                'text-text-secondary hover:bg-surface-container-lowest hover:text-on-surface' => !request()->routeIs('admin.dashboard') && !request()->routeIs('admin.fleet'),
            ])>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined transition-transform duration-300 group-hover:scale-110"
                        @if(request()->routeIs('admin.dashboard') || request()->routeIs('admin.fleet'))
                        style="font-variation-settings: 'FILL' 1;" @endif>directions_car</span>
                    <span class="font-label-md text-[14px]">Sarana</span>
                </div>
                <span class="material-symbols-outlined text-[18px] transition-transform duration-300"
                    :class="open ? 'rotate-180 text-primary' : 'text-outline'">expand_more</span>
            </button>

            <!-- SUBMENU CONTAINER -->
            <div x-show="open" x-collapse x-transition:enter="transition-all ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition-all ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="relative flex flex-col gap-1 mt-2 mb-1 pl-3.5 pr-2 overflow-hidden">

                <!-- SUBMENU ITEM 1 -->
                <a wire:navigate.hover @class([
                    'relative flex items-center gap-3 px-4 py-2.5 rounded-lg cursor-pointer transition-all duration-300 group z-10',
                    'bg-secondary-fixed/50 text-on-secondary-container font-bold' => request()->routeIs('admin.dashboard'),
                    'text-text-secondary font-medium hover:bg-surface-container-lowest hover:text-on-surface hover:translate-x-1' => !request()->routeIs('admin.dashboard'),
                ]) href="{{ route('admin.dashboard') }}">
                    <!-- Active dot indicator (optional, positioned on the line) -->
                    <span class="material-symbols-outlined text-[18px]" @if(request()->routeIs('admin.dashboard'))
                    style="font-variation-settings: 'FILL' 1;" @endif>summarize</span>
                    <span class="text-[13px]">Ringkasan</span>
                </a>


                <!-- SUBMENU ITEM 2 -->
                <a wire:navigate.hover @class([
                    'relative flex items-center gap-3 px-4 py-2.5 rounded-lg cursor-pointer transition-all duration-300 group z-10',
                    'bg-secondary-fixed/50 text-on-secondary-container font-bold' => request()->routeIs('admin.fleet'),
                    'text-text-secondary font-medium hover:bg-surface-container-lowest hover:text-on-surface hover:translate-x-1' => !request()->routeIs('admin.fleet'),
                ]) href="{{ route('admin.fleet') }}">
                    <span class="material-symbols-outlined text-[18px]" @if(request()->routeIs('admin.fleet'))
                    style="font-variation-settings: 'FILL' 1;" @endif>directions_car</span>
                    <span class="text-[13px]">Armada</span>
                </a>
            </div>
        </div>
        <a class="flex items-center gap-3 px-4 py-3 text-text-secondary hover:bg-surface-container-low rounded-lg hover:translate-x-1 transition-transform duration-200 cursor-pointer active:opacity-80"
            href="#">
            <span class="material-symbols-outlined">assignment</span>
            <span class="font-label-md text-[14px]">Permisi</span>
        </a>

        <!-- MENU ASMARA -->
        @if(auth()->user()->canAccessAsmaraAdmin())
        <div
            x-data="{ open: {{ request()->routeIs('asmara.*') ? 'true' : 'false' }} }">
            <button type="button" @click="open = !open" @class([
                'w-full flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300 group',
                'text-primary font-bold' => request()->routeIs('asmara.*'),
                'text-text-secondary hover:bg-surface-container-lowest hover:text-on-surface' => !request()->routeIs('asmara.*'),
            ])>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined transition-transform duration-300 group-hover:scale-110"
                        @if(request()->routeIs('asmara.*'))
                        style="font-variation-settings: 'FILL' 1;" @endif>mail</span>
                    <span class="font-label-md text-[14px]">Asmara</span>
                </div>
                <span class="material-symbols-outlined text-[18px] transition-transform duration-300"
                    :class="open ? 'rotate-180 text-primary' : 'text-outline'">expand_more</span>
            </button>

            <!-- SUBMENU CONTAINER -->
            <div x-show="open" x-collapse x-transition:enter="transition-all ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition-all ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="relative flex flex-col gap-1 mt-2 mb-1 pl-3.5 pr-2 overflow-hidden">

                <!-- SUBMENU ITEM 1 -->
                <a wire:navigate.hover @class([
                    'relative flex items-center gap-3 px-4 py-2.5 rounded-lg cursor-pointer transition-all duration-300 group z-10',
                    'bg-secondary-fixed/50 text-on-secondary-container font-bold' => request()->routeIs('asmara.permohonan'),
                    'text-text-secondary font-medium hover:bg-surface-container-lowest hover:text-on-surface hover:translate-x-1' => !request()->routeIs('asmara.permohonan'),
                ]) href="{{ route('asmara.permohonan') }}">
                    <span class="material-symbols-outlined text-[18px]" @if(request()->routeIs('asmara.permohonan'))
                    style="font-variation-settings: 'FILL' 1;" @endif>mark_email_read</span>
                    <span class="text-[13px]">Riwayat Saya</span>
                </a>

                <!-- SUBMENU ITEM 2 -->
                <a wire:navigate.hover @class([
                    'relative flex items-center gap-3 px-4 py-2.5 rounded-lg cursor-pointer transition-all duration-300 group z-10',
                    'bg-secondary-fixed/50 text-on-secondary-container font-bold' => request()->routeIs('asmara.admin'),
                    'text-text-secondary font-medium hover:bg-surface-container-lowest hover:text-on-surface hover:translate-x-1' => !request()->routeIs('asmara.admin'),
                ]) href="{{ route('asmara.admin') }}">
                    <span class="material-symbols-outlined text-[18px]" @if(request()->routeIs('asmara.admin'))
                    style="font-variation-settings: 'FILL' 1;" @endif>inbox</span>
                    <span class="text-[13px]">Semua Surat</span>
                </a>
            </div>
        </div>
        @endif

        <!-- MENU BONA -->
        @if(auth()->user()->canAccessBonKelola() || auth()->user()->canAccessBonMaster())
        <div
            x-data="{ open: {{ request()->routeIs('bona.*') ? 'true' : 'false' }} }">
            <button type="button" @click="open = !open" @class([
                'w-full flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer transition-all duration-300 group',
                'text-primary font-bold' => request()->routeIs('bona.*'),
                'text-text-secondary hover:bg-surface-container-lowest hover:text-on-surface' => !request()->routeIs('bona.*'),
            ])>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined transition-transform duration-300 group-hover:scale-110"
                        @if(request()->routeIs('bona.*'))
                        style="font-variation-settings: 'FILL' 1;" @endif>inventory_2</span>
                    <span class="font-label-md text-[14px]">Bon ATK</span>
                </div>
                <span class="material-symbols-outlined text-[18px] transition-transform duration-300"
                    :class="open ? 'rotate-180 text-primary' : 'text-outline'">expand_more</span>
            </button>

            <!-- SUBMENU CONTAINER -->
            <div x-show="open" x-collapse x-transition:enter="transition-all ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition-all ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="relative flex flex-col gap-1 mt-2 mb-1 pl-3.5 pr-2 overflow-hidden">

                @if(auth()->user()->canAccessBonKelola())
                <!-- SUBMENU ITEM 1 -->
                <a wire:navigate.hover @class([
                    'relative flex items-center gap-3 px-4 py-2.5 rounded-lg cursor-pointer transition-all duration-300 group z-10',
                    'bg-secondary-fixed/50 text-on-secondary-container font-bold' => request()->routeIs('bona.kelola'),
                    'text-text-secondary font-medium hover:bg-surface-container-lowest hover:text-on-surface hover:translate-x-1' => !request()->routeIs('bona.kelola'),
                ]) href="{{ route('bona.kelola') }}">
                    <span class="material-symbols-outlined text-[18px]" @if(request()->routeIs('bona.kelola'))
                    style="font-variation-settings: 'FILL' 1;" @endif>fact_check</span>
                    <span class="text-[13px]">Persetujuan</span>
                </a>
                @endif

                @if(auth()->user()->canAccessBonMaster())
                <!-- SUBMENU ITEM 2 -->
                <a wire:navigate.hover @class([
                    'relative flex items-center gap-3 px-4 py-2.5 rounded-lg cursor-pointer transition-all duration-300 group z-10',
                    'bg-secondary-fixed/50 text-on-secondary-container font-bold' => request()->routeIs('bona.master-barang'),
                    'text-text-secondary font-medium hover:bg-surface-container-lowest hover:text-on-surface hover:translate-x-1' => !request()->routeIs('bona.master-barang'),
                ]) href="{{ route('bona.master-barang') }}">
                    <span class="material-symbols-outlined text-[18px]" @if(request()->routeIs('bona.master-barang'))
                    style="font-variation-settings: 'FILL' 1;" @endif>category</span>
                    <span class="text-[13px]">Data Barang</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        @if(!auth()->user()->isKepalaKantor())
        <!-- MENU USER -->
        <a wire:navigate.hover @class([
            'flex items-center gap-3 px-4 py-3 rounded-xl cursor-pointer transition-all duration-300 group',
            'text-primary font-bold' => request()->routeIs('admin.user'),
            'text-text-secondary hover:bg-surface-container-lowest hover:text-on-surface' => !request()->routeIs('admin.user'),
        ]) href="{{ route('admin.user') }}">
            <span class="material-symbols-outlined transition-transform duration-300 group-hover:scale-110"
                @if(request()->routeIs('admin.user')) style="font-variation-settings: 'FILL' 1;" @endif>group</span>
            <span class="font-label-md text-[14px]">User</span>
        </a>
        @endif
    </nav>

</aside>