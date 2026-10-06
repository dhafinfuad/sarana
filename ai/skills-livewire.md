# Livewire Skills & Aturan Teknis (Backend Reactivity)

**ATURAN MUTLAK:** Gunakan Livewire HANYA untuk manipulasi data yang membutuhkan proses komputasi di server (Database, Validasi Backend, Session, Export)[cite: 3]. Jangan gunakan Livewire sekadar untuk buka-tutup menu.

## 1. Data Binding (Mengikat Input ke Variabel PHP)
*   **`wire:model="property"`**: Digunakan pada form standar. Data dikirim ke server HANYA saat form di-submit atau aksi lain dipicu. Sangat hemat *resource*.
*   **`wire:model.live="property"`**: Digunakan HANYA jika Anda butuh *real-time update* setiap kali user mengetik atau memilih opsi (contoh: filter pencarian atau *dropdown* dinamis).
*   **`wire:model.blur="property"`**: Data dikirim ke server setelah user selesai mengisi *input* dan pindah ke *field* lain (cocok untuk validasi ketersediaan Plat Nomor).

## 2. Actions (Memicu Fungsi PHP dari UI)
*   **`wire:click="methodName"`**: Memanggil fungsi di komponen Livewire saat elemen diklik (contoh: eksekusi tombol "Setuju" atau "Tolak" pada tabel persetujuan[cite: 2]).
*   **`wire:submit.prevent="save"`**: Wajib diletakkan pada tag `<form>`. Mencegah halaman *reload* bawaan HTML dan mengeksekusi fungsi `save()` di backend untuk memproses form pengajuan[cite: 2].

## 3. Lifecycle Hooks (Reaktivitas Otomatis)
Gunakan ini untuk Flow 2 (Algoritma Cek Ketersediaan Kendaraan)[cite: 2].
*   **`updatedPropertyName($value)`**: Fungsi ajaib ini akan otomatis berjalan setiap kali variabel `$propertyName` berubah.
    ```php
    // Contoh untuk Flow 2:
    public $mulai, $selesai, $mobil_tersedia = [];

    // Otomatis berjalan saat user selesai memilih tanggal 'selesai'
    public function updatedSelesai() {
        if($this->mulai && $this->selesai) {
            $this->mobil_tersedia = Vehicle::whereNotIn(...)->get();
        }
    }
    ```

## 4. Polling (Update Data di Background)
*   **`wire:poll.10s="refreshTable"`**: Wajib digunakan pada Dasbor Admin (Flow 3)[cite: 2]. Fitur ini akan otomatis memanggil fungsi `refreshTable()` setiap 10 detik di *background* untuk memastikan tabel persetujuan selalu *up-to-date* tanpa perlu me-refresh halaman.