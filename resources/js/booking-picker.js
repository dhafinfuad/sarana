/**
 * Pemilih jadwal peminjaman ruangan (modal pengajuan di halaman Ruangan).
 *
 * Dipakai lewat x-data="bookingPicker($wire, {...})". Data jadwal diambil dari Livewire
 * (Ruangan\Index::jadwal) saat modal dibuka atau tanggal diganti; ketukan di jadwal dihitung di
 * browser, dan server tetap memvalidasi ulang saat submit. Waktu dalam menit sejak 00.00.
 */
window.bookingPicker = function ($wire, cfg) {
    return {
        minTanggal: cfg.tanggal,
        tanggal: cfg.tanggal,
        jumlah: '',
        keperluan: '',
        roomId: null,
        s: null,
        e: null,
        picking: false,
        data: { hariIni: true, now: null, buka: cfg.buka, tutup: cfg.tutup, langkah: cfg.langkah, rooms: [] },
        loading: false,
        sending: false,
        ticket: 0,
        editId: null,                 // id peminjaman yang sedang diedit (null = pengajuan baru)

        init() {
            this.$watch('showModal', (open) => {
                if (open) this.load();
                else if (this.editId) this.reset();        // batal edit: jangan bawa data edit ke pengajuan baru
            });
            this.$watch('jumlah', () => {
                const r = this.room;
                if (r && this.kurang(r)) this.clearSelection();
            });
        },

        // ---------- data ----------
        async load() {
            const ticket = ++this.ticket;
            this.loading = true;
            try {
                const d = await $wire.jadwal(this.tanggal, this.editId);
                if (ticket !== this.ticket) return;
                this.data = d;
                this.tanggal = d.tanggal;
                if (this.roomId && !d.rooms.some((r) => r.id === this.roomId)) this.clearSelection();
            } finally {
                if (ticket === this.ticket) this.loading = false;
            }
        },
        pilihTanggal(v) {
            if (!v || v < this.minTanggal) return;
            this.tanggal = v;
            this.clearSelection();
            this.load();
        },
        geser(delta) {
            const d = new Date(this.tanggal + 'T00:00:00');
            d.setDate(d.getDate() + delta);
            const iso = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
            this.pilihTanggal(iso);
        },
        get prevDisabled() { return this.tanggal <= this.minTanggal; },
        get rooms() { return this.data.rooms; },
        get room() { return this.rooms.find((r) => r.id === this.roomId) || null; },
        get jam() {
            const out = [];
            for (let m = this.data.buka; m < this.data.tutup; m += 60) out.push(m);
            return out;
        },

        // ---------- format ----------
        fmt(m) { return String(Math.floor(m / 60)).padStart(2, '0') + '.' + String(m % 60).padStart(2, '0'); },
        dur(m) {
            const h = Math.floor(m / 60), r = m % 60;
            return (h ? h + ' jam' : '') + (h && r ? ' ' : '') + (r ? r + ' menit' : '');
        },
        pct(m) { return ((m - this.data.buka) / (this.data.tutup - this.data.buka)) * 100; },
        barStyle(b) {
            const s = Math.max(b.s, this.data.buka), e = Math.min(b.e, this.data.tutup);
            if (e <= s) return 'display:none';
            return 'left:' + this.pct(s) + '%;width:' + (this.pct(e) - this.pct(s)) + '%';
        },
        tickStyle(m) { return 'left:' + this.pct(m) + '%'; },

        // ---------- jadwal ----------
        /** Pemakaian ruangan + bagian hari ini yang sudah lewat (tidak bisa dipilih). */
        intervals(r) {
            const out = r.busy.slice();
            if (this.data.now != null && this.data.now > this.data.buka) {
                out.push({ s: this.data.buka, e: Math.min(this.data.now, this.data.tutup), st: 'past' });
            }
            return out.sort((a, b) => a.s - b.s);
        },
        kurang(r) {
            const n = parseInt(this.jumlah, 10);
            return n > 0 && n > r.kapasitas;
        },
        clearSelection() { this.roomId = null; this.s = null; this.e = null; this.picking = false; },

        /** Ketukan di jadwal: ketukan pertama = awal, kedua = akhir (per kelipatan langkah). */
        ketuk(r, ev) {
            if (this.kurang(r)) return;
            const rect = ev.currentTarget.getBoundingClientRect();
            const { buka, tutup, langkah } = this.data;
            const t = Math.max(buka, Math.min(tutup - 1, Math.floor(buka + ((ev.clientX - rect.left) / rect.width) * (tutup - buka))));
            const us = Math.floor(t / langkah) * langkah, ue = us + langkah;
            const list = this.intervals(r);
            if (list.some((x) => x.s <= t && x.e > t)) return;               // ketukan di bagian terpakai

            if (this.roomId === r.id && this.picking && t >= this.s) {
                const blocked = list.some((x) => x.s >= this.s && x.s <= t);  // ada pemakaian di antara awal dan ketukan
                if (!blocked) { this.e = ue; this.picking = false; return; }
            }
            let a = us, b = ue;
            list.forEach((x) => { if (x.e <= t) a = Math.max(a, x.e); if (x.s > t) b = Math.min(b, x.s); });
            if (b - a < langkah) return;
            this.roomId = r.id; this.s = a; this.e = b; this.picking = true;
        },
        ulangi() { this.clearSelection(); },
        /** Dibuka dari tombol Edit (event open-room-edit): isi pemilih dari data peminjaman. */
        mulaiEdit(d) {
            this.editId = d.id;
            this.tanggal = d.tanggal;
            this.jumlah = String(d.jumlah);
            this.keperluan = d.keperluan;
            this.roomId = d.roomId;
            this.s = d.mulai;
            this.e = d.selesai;
            this.picking = false;
            this.load();
        },
        reset() {
            this.editId = null;
            this.clearSelection();
            this.jumlah = '';
            this.keperluan = '';
            this.tanggal = this.minTanggal;
        },

        // ---------- panel waktu ----------
        mulaiOptions() {
            const out = [];
            for (let m = this.data.buka; m < this.data.tutup; m += this.data.langkah) {
                out.push({ v: m, off: this.data.now != null && m < this.data.now });
            }
            return out;
        },
        selesaiOptions() {
            const out = [];
            for (let m = this.s + this.data.langkah; m <= this.data.tutup; m += this.data.langkah) out.push(m);
            return out;
        },
        setMulai(v) {
            this.s = parseInt(v, 10);
            if (this.e <= this.s) this.e = Math.min(this.data.tutup, this.s + 60);
            this.picking = false;
        },
        setSelesai(v) { this.e = parseInt(v, 10); this.picking = false; },
        get conflict() {
            const r = this.room;
            if (!r || this.s == null) return null;
            return this.intervals(r).find((x) => x.s < this.e && x.e > this.s) || null;
        },
        conflictText(c) {
            if (c.st === 'past') return 'Waktu itu sudah lewat. Pilih waktu berikutnya.';
            return 'Bentrok: ruangan ini ' + (c.st === 'ok' ? 'terpakai ' : 'ditahan ') + this.fmt(c.s) + '–' + this.fmt(c.e) + '. Ubah waktunya.';
        },
        get valid() {
            const r = this.room, n = parseInt(this.jumlah, 10);
            return !!r && n >= 1 && n <= r.kapasitas && this.e - this.s >= this.data.langkah && !this.conflict && this.keperluan.trim().length >= 5;
        },

        // ---------- kirim ----------
        async kirim() {
            if (!this.valid || this.sending) return;
            this.sending = true;
            try {
                $wire.$set('roomId', String(this.roomId), false);
                $wire.$set('jumlahPeserta', String(parseInt(this.jumlah, 10)), false);
                $wire.$set('keperluan', this.keperluan, false);
                $wire.$set('tanggal', this.tanggal, false);
                $wire.$set('mulaiMenit', this.s, false);
                $wire.$set('selesaiMenit', this.e, false);
                await (this.editId ? $wire.update() : $wire.submit());
            } finally {
                this.sending = false;
            }
            // Bila server menolak (mis. bentrok), muat ulang jadwal agar data terbaru terlihat.
            if (this.showModal) this.load();
        },
    };
};
