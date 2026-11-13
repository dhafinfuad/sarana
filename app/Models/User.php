<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'nip_pendek', 'nip', 'jabatan', 'seksi', 'target_kegiatan', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Identifier untuk otentikasi menggunakan nip_pendek (bukan email).
     */
    public string $authIdentifierName = 'nip_pendek';

    // -----------------------------------------------------------------------
    // Casts
    // -----------------------------------------------------------------------

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // -----------------------------------------------------------------------
    // Auth override: gunakan nip_pendek sebagai username
    // -----------------------------------------------------------------------

    public function getAuthIdentifierName(): string
    {
        return 'nip_pendek';
    }

    // -----------------------------------------------------------------------
    // Helper Methods (Strict Typing & PLH/PLT Support)
    // -----------------------------------------------------------------------

    /**
     * Property penampung cache aktif PLH/PLT per request.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, PlhPlt>|null
     */
    protected ?\Illuminate\Database\Eloquent\Collection $activePlhPltCache = null;

    /**
     * Ambil seluruh data PLH/PLT aktif milik user ini.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, PlhPlt>
     */
    public function getActivePlhPlt(): \Illuminate\Database\Eloquent\Collection
    {
        if ($this->relationLoaded('activePlhPlt')) {
            return $this->activePlhPlt;
        }

        if ($this->activePlhPltCache === null) {
            $this->activePlhPltCache = $this->activePlhPlt()->get();
        }

        return $this->activePlhPltCache;
    }

    /**
     * Mengembalikan daftar nama seksi yang dipimpin/dinaungi user ini.
     * Mencakup seksi definitif (jika Kepala Seksi) dan seksi PLH/PLT aktif.
     *
     * @return array<string>
     */
    public function getManagedSeksiList(): array
    {
        $list = [];

        if ($this->jabatan === 'Kepala Seksi' && ! empty($this->seksi)) {
            $list[] = $this->seksi;
        }

        foreach ($this->getActivePlhPlt() as $plh) {
            $seksiName = $plh->getCleanSeksiName();
            if (! empty($seksiName) && ! in_array($seksiName, $list, true)) {
                $list[] = $seksiName;
            }
        }

        return $list;
    }

    /** Apakah user ini adalah Administrator? */
    public function isAdministrator(): bool
    {
        return $this->jabatan === 'Administrator';
    }

    /** Apakah user ini adalah Kepala Kantor (definitif atau PLH/PLT)? */
    public function isKepalaKantor(): bool
    {
        if ($this->jabatan === 'Kepala Kantor') {
            return true;
        }

        foreach ($this->getActivePlhPlt() as $plh) {
            if (stripos($plh->posisi, 'Kepala Kantor') !== false) {
                return true;
            }
        }

        return false;
    }

    /** Apakah user ini adalah Kepala Seksi (definitif atau merangkap PLH/PLT)? */
    public function isKepalaSeksi(): bool
    {
        if ($this->jabatan === 'Kepala Seksi') {
            return true;
        }

        return ! empty($this->getManagedSeksiList());
    }

    // -----------------------------------------------------------------------
    // Hak Akses (Admin → Hak Akses): matriks per peran, lihat config/hak_akses.php
    // -----------------------------------------------------------------------

    /** @var array<int, string>|null */
    protected ?array $rolesCache = null;

    /**
     * Peran pengguna, dihitung otomatis dari jabatan, seksi, dan PLH/PLT aktif.
     * Satu pengguna bisa punya beberapa peran; aksesnya digabung (OR).
     *
     * @return array<int, string>
     */
    public function roles(): array
    {
        if ($this->rolesCache !== null) {
            return $this->rolesCache;
        }

        $roles = [];
        if ($this->isAdministrator()) {
            $roles[] = 'administrator';
        }
        if ($this->isKepalaKantor()) {
            $roles[] = 'kepala_kantor';
        }
        if ($this->isKepalaSeksi()) {
            $roles[] = 'kepala_seksi';
        }
        if ($this->isSupervisor()) {
            $roles[] = 'supervisor';
        }
        if ($this->isSubbagUmum()) {
            $roles[] = 'subbag_umum';
        }
        if ($this->isPenjaminanKualitasData()) {
            $roles[] = 'pkd';
        }
        // Semua pengguna adalah Pegawai, kecuali Kepala Kantor definitif.
        if ($this->jabatan !== 'Kepala Kantor') {
            $roles[] = 'pegawai';
        }

        return $this->rolesCache = $roles;
    }

    /** Apakah pengguna boleh memakai menu/halaman/tombol dengan kunci ini? */
    public function hasAccess(string $key): bool
    {
        return \App\Support\HakAkses::allows($this->roles(), $key);
    }

    /** Halaman awal setelah login: halaman pertama yang boleh dibuka, menurut urutan prioritas. */
    public function homeRoute(): string
    {
        $candidates = [
            'kendaraan.dashboard' => 'admin.dashboard',
            'kendaraan.peminjaman' => 'peminjaman.index',
            'permisi.persetujuan' => 'permisi.persetujuan',
            'permisi.permohonan' => 'permisi.permohonan',
            'ruangan.peminjaman' => 'ruangan.peminjaman',
            'asmara.permohonan' => 'asmara.permohonan',
            'bona.permohonan' => 'bona.permohonan',
        ];

        foreach ($candidates as $key => $route) {
            if ($this->hasAccess($key)) {
                return route($route);
            }
        }

        return route('asmara.permohonan');
    }

    /** Apakah user ini adalah Supervisor? */
    public function isSupervisor(): bool
    {
        return $this->jabatan === 'Supervisor';
    }

    /** Apakah user ini dapat mengakses halaman Persetujuan Permisi? */
    public function canAccessPersetujuanPermisi(): bool
    {
        return $this->hasAccess('permisi.persetujuan');
    }

    /** Apakah user ini dapat mengakses halaman Permohonan Izin? */
    public function canAccessPermohonanPermisi(): bool
    {
        return $this->hasAccess('permisi.permohonan');
    }

    /** Apakah user ini dapat mengakses halaman Sarana > Peminjaman? */
    public function canAccessPeminjaman(): bool
    {
        return $this->hasAccess('kendaraan.peminjaman');
    }

    /** Apakah user ini adalah Pegawai (AR, Pelaksana, atau Kepala Seksi yang mengajukan)? */
    public function isPegawai(): bool
    {
        return !$this->canAccessAdminDashboard();
    }

    /** Apakah user ini dapat mengakses dashboard admin? */
    public function canAccessAdminDashboard(): bool
    {
        return $this->hasAccess('kendaraan.dashboard');
    }

    /** Apakah user ini dapat melakukan manajemen/perubahan data pada armada? */
    public function canManageArmada(): bool
    {
        return $this->hasAccess('kendaraan.armada.kelola');
    }

    /** Apakah user ini dapat melakukan aksi manajemen booking? */
    public function canManageBookings(): bool
    {
        return $this->hasAccess('kendaraan.dashboard.aksi');
    }

    // -----------------------------------------------------------------------
    // Hak Akses BONA (Permintaan ATK)
    // -----------------------------------------------------------------------

    public function isSubbagUmum(): bool
    {
        if ($this->nip_pendek === '060092694') {
            return true;
        }

        if (trim((string) $this->seksi) === 'Subbagian Umum dan Kepatuhan Internal') {
            return true;
        }

        foreach ($this->getActivePlhPlt() as $plh) {
            if ($plh->getCleanSeksiName() === 'Subbagian Umum dan Kepatuhan Internal') {
                return true;
            }
        }

        return false;
    }

    public function canAccessBonPermohonan(): bool
    {
        return $this->hasAccess('bona.permohonan');
    }

    public function canAccessBonKelola(): bool
    {
        return $this->hasAccess('bona.kelola');
    }

    public function canAccessBonMaster(): bool
    {
        return $this->hasAccess('bona.master');
    }

    // -----------------------------------------------------------------------
    // Hak Akses Asmara (Surat Keluar)
    // -----------------------------------------------------------------------

    public function canAccessAsmaraPermohonan(): bool
    {
        return $this->hasAccess('asmara.permohonan');
    }

    public function canAccessAsmaraAdmin(): bool
    {
        return $this->hasAccess('asmara.admin');
    }

    public function isPenjaminanKualitasData(): bool
    {
        return $this->seksi === 'Seksi Penjaminan Kualitas Data';
    }

    // -----------------------------------------------------------------------
    // Relasi Eloquent
    // -----------------------------------------------------------------------

    /**
     * Semua riwayat penugasan PLH/PLT pegawai ini.
     *
     * @return HasMany<PlhPlt, $this>
     */
    public function plhPltAssignments(): HasMany
    {
        return $this->hasMany(PlhPlt::class, 'pegawai_id');
    }

    /**
     * Penugasan PLH/PLT yang saat ini sedang aktif.
     *
     * @return HasMany<PlhPlt, $this>
     */
    public function activePlhPlt(): HasMany
    {
        return $this->hasMany(PlhPlt::class, 'pegawai_id')->aktif();
    }

    /**
     * Mendapatkan array badge label penugasan PLH/PLT aktif.
     * Contoh: ['PLT Kepala Seksi Pelayanan']
     *
     * @return array<string>
     */
    public function getPlhPltBadges(): array
    {
        $badges = [];
        foreach ($this->getActivePlhPlt() as $plh) {
            $badges[] = $plh->getDisplayTitle();
        }
        return $badges;
    }

    /**
     * Mendapatkan string lengkap tampilan jabatan dan penugasan.
     * Contoh: "Kepala Seksi (PLT Kepala Seksi Pelayanan)"
     */
    public function getFullDisplayTitle(): string
    {
        $title = $this->jabatan ?? 'Pegawai';
        $badges = $this->getPlhPltBadges();
        if (! empty($badges)) {
            $title .= ' (' . implode(', ', $badges) . ')';
        }
        return $title;
    }

    /**
     * Peminjaman yang dibuat oleh pegawai ini.
     *
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    /**
     * Peminjaman yang diproses (disetujui/ditolak/dibatalkan) oleh user ini (admin).
     *
     * @return HasMany<Booking, $this>
     */
    public function processedBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'processed_by');
    }
}
