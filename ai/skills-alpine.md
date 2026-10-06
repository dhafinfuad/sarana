# Alpine.js Skills & Aturan Teknis (Frontend DOM Manipulation)

**ATURAN MUTLAK:** Gunakan Alpine.js HANYA untuk manipulasi antarmuka (DOM) lokal yang sifatnya visual murni dan tidak membutuhkan komunikasi ke server/database (contoh: *Modal*, *Dropdown*, *Toggle Password*, *Tabs*)[cite: 3].

## 1. Inisialisasi State Lokal
*   **`x-data="{ property: value }"`**: Mendefinisikan area kerja Alpine dan variabel lokal. Wajib diletakkan di elemen induk (wrapper).
    ```html
    <!-- Contoh dasar Modal: -->
    <div x-data="{ openModal: false }"> ... </div>
    ```

## 2. Kondisional (Show / Hide Elemen)
*   **`x-show="property"`**: Menampilkan elemen jika nilainya `true`, menyembunyikan jika `false` dengan `display: none`. Gunakan ini untuk pop-up *Modal* CRUD Kendaraan (Flow 4)[cite: 2].
*   **`x-transition`**: Tambahkan atribut ini bersamaan dengan `x-show` untuk memberikan efek transisi (fade) otomatis bawaan Alpine yang halus.

## 3. Event Listeners (Menangkap Aksi User)
*   **`@click="property = !property"`**: Atribut ini (alias dari `x-on:click`) digunakan untuk mengubah nilai variabel. Sangat cocok untuk tombol *toggle*.
*   **`@click.outside="property = false"`**: Atribut krusial untuk UX. Menutup *Modal* atau *Dropdown* ketika user mengklik area di luar elemen tersebut.

## 4. Manipulasi Atribut (x-bind)
Digunakan untuk mengubah atribut HTML secara dinamis, contohnya untuk fitur "Show/Hide Password" pada halaman Login (Flow 1)[cite: 2].
*   **`:type="property ? 'text' : 'password'"`**: Atribut ini (alias dari `x-bind:type`) akan mengubah tipe input.
    ```html
    <!-- Contoh Flow 1: Toggle Show/Hide Password -->
    <div x-data="{ show: false }">
        <input :type="show ? 'text' : 'password'" wire:model="password">
        <button type="button" @click="show = !show">
            <span x-text="show ? 'Sembunyikan' : 'Lihat'"></span>
        </button>
    </div>
    ```

## 5. Integrasi Alpine dan Livewire (Entangle)
Jika Anda perlu menggabungkan *state* UI Alpine dengan variabel *backend* Livewire:
*   **`x-data="{ showModal: @entangle('isModalOpen') }"`**: Mengikat variabel Alpine (`showModal`) dengan properti Livewire (`isModalOpen`). Jika server mengubah nilai, modal terbuka. Jika user menutup modal, server tahu.