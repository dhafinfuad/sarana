# Sistem Desain (Design System) & Estetika UI
**Aturan Absolut untuk Agent AI Frontend:** Setiap kali Anda ditugaskan membuat antarmuka (UI), Anda WAJIB mematuhi seluruh aturan styling di bawah ini tanpa pengecualian. Tema aplikasi ini adalah **Precision Glass Analytics** (Glassmorphic-Corporate hybrid).

## 1. Fundamental Styling & Identity
* **Framework Wajib:** Gunakan murni Tailwind CSS. 
* **Brand Personality:** Otoritatif, analitis, dan modern. Dirancang untuk pemantauan data berisiko tinggi. UI memprioritaskan keterbacaan data padat (*information density*) tanpa terasa penuh.
* **Pendekatan Layout:** *Fixed-Fluid Hybrid*. Gunakan *container* di tengah dengan *max-width* `max-w-[1600px]` untuk layar *desktop*.

## 2. Palet Warna (Deep Sea & Glass)
Gunakan kode warna Hex secara spesifik menggunakan *arbitrary values* Tailwind (contoh: `bg-[#f7f9ff]`) atau petakan di `tailwind.config.js`.
* **Background Canvas (Main):** Sangat sejuk dan cerah, gunakan `#f7f9ff`. Hindari putih murni untuk *body* agar mata tidak lelah.
* **Surface Glass (Cards/Panels):** Terapkan putih transparan `bg-white/85` (atau `rgba(255, 255, 255, 0.85)`).
* **Primary (Brand):** Biru korporat `#004875` untuk teks utama interaktif, `#07629c` (surface tint), dan `#cfe5ff` untuk *primary-container*.
* **Semantic Accents:** * Sukses/Positif: Hijau Emerald (`text-[#059669]` dengan *background* `#d1fae5`).
  * Peringatan: Amber.
  * Error: Merah (`text-[#ba1a1a]` dengan *background* `#ffdad6`).
* **Teks Utama:** `#151c23` (on-surface) dan sekunder `#414750` (on-surface-variant).

## 3. Elevation & Glassmorphic Depth
*Kedalaman UI tidak diciptakan dengan bayangan tebal, melainkan tumpukan material.*
* **Efek Kaca (Glass Panel):** Semua *Card* dan *Container* WAJIB menggunakan kelas `backdrop-blur-md` (blur sekitar 12px) dipadu dengan `bg-white/85`.
* **Borders:** Jangan gunakan *shadow* besar untuk membatasi *card*. Gunakan border tipis 1px padat `border border-[#c0c7d2]/40` (Outline Muted).
* **Hover States:** Saat *card* di-hover, tambahkan bayangan *ambient* halus: `hover:shadow-[0_4px_20px_-4px_rgba(0,0,0,0.08)]`.

## 4. Tipografi (Inter)
* **Font Wajib:** **Inter** (sans-serif) untuk presisi data.
* **Hierarki Font-Weight:** * `font-bold` (700) untuk *Header* halaman.
  * `font-semibold` (600) untuk *Title* pada komponen/card.
  * `font-medium` (500) untuk label primer.
* **Tracking (Letter Spacing):** Terapkan `tracking-tight` (-0.02em) pada ukuran font yang besar (*Display/Header*) agar terasa kompak.
* **Data Numerik:** Angka atau metrik utama pada kartu analitik harus berukuran besar (`text-2xl` atau `text-3xl`) dengan satuan/label di sebelahnya menggunakan teks berukuran medium/kecil.

## 5. Bentuk (Shapes) & Spacing
*Bentuk mengikuti bahasa "Softly Geometric".*
* **Cards & Panels:** Sudut membulat medium ke besar, gunakan `rounded-xl` (24px) atau `rounded-2xl` untuk melembutkan kesan analitik.
* **Buttons & Inputs (Form):** Gunakan `rounded-lg` (16px) atau `rounded-[8px]` untuk membedakan fungsi "aksi" dengan "kontainer".
* **Badges / Trend Chips:** Gunakan bentuk pil `rounded-full` (9999px).
* **Spacing Rhythm:** Jarak antar seksi (*Section gap*) adalah `gap-8` (32px), sedangkan jarak di dalam kartu (*Internal card padding*) adalah `p-6` (24px).

## 6. Komponen Spesifik & Aksesibilitas PWA
* **Top Navigation:** Aplikasi menggunakan bilah navigasi horizontal di atas (*TopNavBar*) untuk memaksimalkan ruang kerja, bukan *sidebar* kiri yang lebar.
* **Progress Bars:** Harus memiliki *track* abu-abu pucat dan *fill* warna *primary*, tinggi standar 10px, dan ujung yang membulat penuh (`rounded-full`).
* **Grafik (Charts):** Garis *grid* harus menggunakan warna pudar `rgba(192,199,210,0.5)`. Garis data melengkung (tension 0.4), dan grafik area wajib menggunakan gradasi linier vertikal dari warna *brand* memudar ke transparan ke bawah.
* **Progressive Web App (PWA):** *Header/Top Bar* WAJIB menggunakan kelas `pt-safe`, dan ruang paling bawah layar WAJIB dilindungi dengan `pb-safe`.


---
name: Precision Glass Analytics
colors:
  surface: '#f7f9ff'
  surface-dim: '#d4dae5'
  surface-bright: '#f7f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#edf4fe'
  surface-container: '#e8eef9'
  surface-container-high: '#e2e9f3'
  surface-container-highest: '#dce3ed'
  on-surface: '#151c23'
  on-surface-variant: '#414750'
  inverse-surface: '#2a3139'
  inverse-on-surface: '#ebf1fc'
  outline: '#717881'
  outline-variant: '#c1c7d1'
  surface-tint: '#07629c'
  primary: '#004875'
  on-primary: '#ffffff'
  primary-container: '#00609a'
  on-primary-container: '#b6d8ff'
  inverse-primary: '#99cbff'
  secondary: '#49607a'
  on-secondary: '#ffffff'
  secondary-container: '#c7dffe'
  on-secondary-container: '#4c637d'
  tertiary: '#00486e'
  on-tertiary: '#ffffff'
  tertiary-container: '#006191'
  on-tertiary-container: '#aed9ff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#cfe5ff'
  primary-fixed-dim: '#99cbff'
  on-primary-fixed: '#001d34'
  on-primary-fixed-variant: '#004a78'
  secondary-fixed: '#d0e4ff'
  secondary-fixed-dim: '#b1c9e7'
  on-secondary-fixed: '#021d34'
  on-secondary-fixed-variant: '#314961'
  tertiary-fixed: '#cbe6ff'
  tertiary-fixed-dim: '#90cdff'
  on-tertiary-fixed: '#001e30'
  on-tertiary-fixed-variant: '#004b71'
  background: '#f7f9ff'
  on-background: '#151c23'
  surface-variant: '#dce3ed'
  success-green: '#059669'
  success-bg: '#d1fae5'
  accent-indigo: '#4f46e5'
  accent-indigo-bg: '#e0e7ff'
  surface-glass: rgba(255, 255, 255, 0.85)
  background-main: '#f7f9ff'
  outline-muted: rgba(192, 199, 210, 0.4)
typography:
  display-lg:
    fontFamily: Inter
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 34px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  title-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  title-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '500'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-lg:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.01em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  canvas-max-width: 1600px
  page-padding: 2rem
  section-gap: 2rem
  card-padding: 1.5rem
  grid-gutter: 1.5rem
  inline-gap-sm: 0.5rem
  inline-gap-md: 0.75rem
---