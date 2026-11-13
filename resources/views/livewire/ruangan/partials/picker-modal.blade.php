{{--
    Modal pengajuan peminjaman ruangan: pemilih jadwal (tanggal, jumlah orang, ketuk jadwal per 15 menit).
    Logika di resources/js/booking-picker.js; server memvalidasi ulang di Ruangan\Index::submit().
    Dipakai di halaman Peminjaman dan Persetujuan (tombol header) untuk pengajuan baru, dan oleh tombol Edit (mode edit). Butuh x-data induk dengan showModal.
--}}
@php
    $pickerCfg = [
        'tanggal' => now()->toDateString(),
        'buka'    => (int) config('ruangan.jam_buka') * 60,
        'tutup'   => (int) config('ruangan.jam_tutup') * 60,
        'langkah' => (int) config('ruangan.langkah'),
    ];
    $inputCls = 'px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-brand-600 text-xs';
@endphp

<div x-data="bookingPicker($wire, @js($pickerCfg))" @close-room-modal.window="reset()" @open-room-edit.window="mulaiEdit($event.detail); showModal = true">
    <x-modal show="showModal" maxWidth="max-w-3xl">
        <x-slot:header>
            <div class="flex items-center justify-between w-full">
                <h3 class="text-base font-bold text-slate-900" x-text="editId ? 'Edit Peminjaman Ruangan' : 'Pengajuan Peminjaman Ruangan'">Pengajuan Peminjaman Ruangan</h3>
                <button @click="showModal = false" type="button" aria-label="Tutup" class="text-slate-400 hover:text-slate-700 text-xl leading-none cursor-pointer">&times;</button>
            </div>
        </x-slot:header>

        {{-- Filter: tanggal dan jumlah orang --}}
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="geser(-1)" :disabled="prevDisabled" aria-label="Hari sebelumnya"
                class="w-9 h-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-bold text-slate-600 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">&lsaquo;</button>
            <input type="date" x-model="tanggal" :min="minTanggal" @change="pilihTanggal($event.target.value)" aria-label="Tanggal" class="{{ $inputCls }} w-44">
            <button type="button" @click="geser(1)" aria-label="Hari berikutnya"
                class="w-9 h-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-bold text-slate-600 cursor-pointer">&rsaquo;</button>
            <input type="number" min="1" inputmode="numeric" x-model="jumlah" placeholder="Jumlah orang" aria-label="Jumlah orang" class="{{ $inputCls }} w-36 ml-1">
            <span x-show="loading" class="material-symbols-outlined animate-spin text-brand-600 text-[18px] ml-1" aria-label="Memuat jadwal">sync</span>
        </div>

        {{-- Legenda --}}
        <div class="flex flex-wrap gap-x-4 gap-y-1.5 text-[11.5px] text-slate-500">
            <span class="inline-flex items-center gap-1.5"><i class="w-3 h-3 rounded-[3px] border border-slate-200 bg-slate-100"></i>Kosong</span>
            <span class="inline-flex items-center gap-1.5"><i class="w-3 h-3 rounded-[3px] bg-slate-400"></i>Terpakai</span>
            <span class="inline-flex items-center gap-1.5"><i class="w-3 h-3 rounded-[3px] bg-amber-50 border border-dashed border-amber-400"></i>Ditahan (menunggu persetujuan)</span>
            <span class="inline-flex items-center gap-1.5"><i class="w-3 h-3 rounded-[3px] bg-brand-600"></i>Pilihan Anda</span>
        </div>

        {{-- Daftar ruangan dengan jadwalnya --}}
        <div class="space-y-2">
            <template x-for="r in rooms" :key="r.id">
                <div class="border border-slate-200 rounded-2xl px-3.5 py-3" :class="kurang(r) ? 'bg-slate-50 opacity-60' : 'bg-white'">
                    <div class="flex flex-wrap items-baseline gap-x-2.5 gap-y-0.5 mb-2">
                        <span class="text-sm font-bold text-slate-900" x-text="r.nama"></span>
                        <span class="text-xs text-slate-500" x-text="[r.lokasi, r.kapasitas + ' orang', r.fasilitas].filter(Boolean).join(' · ')"></span>
                        <span x-show="kurang(r)" class="text-[11px] font-semibold text-rose-600" x-text="'Kapasitas kurang, butuh ' + jumlah + ' orang'"></span>
                    </div>
                    <div class="relative h-7 rounded-lg border border-slate-200 bg-slate-100/70 overflow-hidden"
                        :class="kurang(r) ? 'cursor-not-allowed' : 'cursor-pointer'" @click="ketuk(r, $event)"
                        role="group" :aria-label="'Jadwal ' + r.nama">
                        <template x-for="m in jam.slice(1)" :key="m"><i class="absolute inset-y-0 w-px bg-slate-200" :style="tickStyle(m)"></i></template>
                        <template x-for="(b, i) in intervals(r)" :key="i">
                            <b class="absolute inset-y-0" :style="barStyle(b)"
                                :class="{ 'bg-slate-400': b.st === 'ok', 'bg-amber-50 border-x border-dashed border-amber-400': b.st === 'hold', 'bg-slate-200': b.st === 'past' }"
                                :title="b.st === 'past' ? 'Sudah lewat' : (b.st === 'ok' ? 'Terpakai ' : 'Ditahan ') + fmt(b.s) + '–' + fmt(b.e)"></b>
                        </template>
                        <template x-if="roomId === r.id && s != null">
                            <b class="absolute inset-y-0 bg-brand-600/90" :style="barStyle({ s: s, e: e })" :title="'Pilihan ' + fmt(s) + '\u2013' + fmt(e)"></b>
                        </template>
                    </div>
                    <div class="grid mt-1 text-[10.5px] text-slate-500 tabular-nums" :style="'grid-template-columns:repeat(' + jam.length + ',minmax(0,1fr))'">
                        <template x-for="m in jam" :key="m"><span class="pl-0.5" x-text="String(m / 60).padStart(2, '0')"></span></template>
                    </div>
                </div>
            </template>
            <p x-show="!loading && rooms.length === 0" class="text-slate-400 py-6 text-center">Belum ada ruangan yang dapat dipinjam.</p>
        </div>

        {{-- Panel waktu dan keperluan --}}
        <div x-show="!roomId" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-slate-500">Belum ada waktu dipilih.</div>
        <div x-show="roomId" class="rounded-2xl border border-brand-600 bg-brand-50 p-4 space-y-3">
            <p class="font-bold text-slate-900"><span x-text="room ? room.nama : ''"></span>
                <span class="font-medium text-slate-500" x-text="s != null ? ' · ' + fmt(s) + '–' + fmt(e) + ' · ' + dur(e - s) : ''"></span></p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5" for="pk-mulai">Mulai</label>
                    <select id="pk-mulai" @change="setMulai($event.target.value)" class="{{ $inputCls }} w-full bg-white">
                        <template x-for="o in mulaiOptions()" :key="o.v"><option :value="o.v" :selected="o.v === s" :disabled="o.off" x-text="fmt(o.v)"></option></template>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5" for="pk-selesai">Selesai</label>
                    <select id="pk-selesai" @change="setSelesai($event.target.value)" class="{{ $inputCls }} w-full bg-white">
                        <template x-for="m in (s != null ? selesaiOptions() : [])" :key="m"><option :value="m" :selected="m === e" x-text="fmt(m)"></option></template>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1.5" for="pk-keperluan">Keperluan <span class="text-rose-500">*</span></label>
                    <input type="text" id="pk-keperluan" x-model="keperluan" placeholder="Contoh: Rapat koordinasi bulanan" class="{{ $inputCls }} w-full bg-white">
                </div>
            </div>
            <p x-show="conflict" class="text-[12px] font-semibold text-rose-600" x-text="conflict ? conflictText(conflict) : ''"></p>
        </div>

        {{-- Pesan dari server (mis. bentrok saat dikirim, kapasitas, jam operasional) --}}
        @foreach(['roomId', 'jumlahPeserta', 'tanggal', 'mulaiMenit', 'selesaiMenit', 'keperluan'] as $field)
            @error($field) <p class="text-[12px] font-semibold text-rose-600">{{ $message }}</p> @enderror
        @endforeach

        <x-slot:footer>
            <button type="button" @click="ulangi()" :disabled="!roomId"
                class="px-5 py-2 rounded-xl font-bold text-slate-600 border border-slate-200 hover:bg-slate-100 transition text-xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                Ulangi
            </button>
            <button type="button" @click="kirim()" :disabled="!valid || sending"
                class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs shadow-md shadow-brand-600/25 transition cursor-pointer flex items-center gap-1.5 disabled:bg-slate-200 disabled:text-slate-400 disabled:shadow-none disabled:cursor-not-allowed">
                <span x-text="editId ? 'Simpan Perubahan' : 'Ajukan Peminjaman'">Ajukan Peminjaman</span>
                <span x-show="sending" class="material-symbols-outlined animate-spin text-[14px]">refresh</span>
            </button>
        </x-slot:footer>
    </x-modal>
</div>
