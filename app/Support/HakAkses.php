<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Matriks hak akses per peran (Admin → Hak Akses).
 * Nilai efektif = bawaan dari config/hak_akses.php, ditimpa baris di tabel role_permissions.
 */
final class HakAkses
{
    public const ADMIN = 'administrator';
    private const CACHE_KEY = 'hak_akses.overrides';

    /** @var array<string, array<string, bool>>|null cache per-request */
    private static ?array $overrides = null;

    /** @return array<string, string> kode peran => label */
    public static function roles(): array
    {
        return config('hak_akses.roles');
    }

    /** @return array<string, array<string, array<string, mixed>>> modul => [kunci => meta] */
    public static function modules(): array
    {
        return config('hak_akses.modules');
    }

    /** @return array<string, array<string, mixed>> kunci => meta (datar) */
    public static function catalog(): array
    {
        return array_merge(...array_values(self::modules()));
    }

    public static function isLocked(string $key): bool
    {
        return in_array($key, config('hak_akses.locked', []), true);
    }

    public static function defaultAllows(string $role, string $key): bool
    {
        if ($role === self::ADMIN) {
            return true;
        }

        return ! self::isLocked($key) && in_array($role, self::catalog()[$key]['default'] ?? [], true);
    }

    /** Nilai efektif satu sel matriks. */
    public static function allowsRole(string $role, string $key): bool
    {
        if ($role === self::ADMIN) {
            return true;                                   // kolom Administrator dikunci: selalu boleh
        }
        if (self::isLocked($key)) {
            return false;                                  // baris terkunci hanya untuk Administrator
        }

        return self::overrides()[$key][$role] ?? self::defaultAllows($role, $key);
    }

    /** Apakah salah satu peran mengizinkan kunci ini (aturan OR). Kunci tak dikenal = ditolak. */
    public static function allows(array $roles, string $key): bool
    {
        if (! array_key_exists($key, self::catalog())) {
            return false;
        }

        foreach ($roles as $role) {
            if (self::allowsRole($role, $key)) {
                return true;
            }
        }

        return false;
    }

    /** @return array<string, array<string, bool>> matriks efektif [kunci][peran] */
    public static function matrix(): array
    {
        $out = [];
        foreach (array_keys(self::catalog()) as $key) {
            foreach (array_keys(self::roles()) as $role) {
                $out[$key][$role] = self::allowsRole($role, $key);
            }
        }

        return $out;
    }

    /**
     * Simpan matriks: hanya sel yang berbeda dari bawaan yang disimpan. Mengembalikan daftar perubahan.
     *
     * @param  array<string, array<string, bool>>  $matrix
     * @return array<int, string>
     */
    public static function save(array $matrix, User $by): array
    {
        $before = self::matrix();
        $rows = [];
        $changes = [];

        foreach (self::catalog() as $key => $meta) {
            if (self::isLocked($key)) {
                continue;
            }
            foreach (array_keys(self::roles()) as $role) {
                if ($role === self::ADMIN) {
                    continue;
                }
                $value = (bool) ($matrix[$key][$role] ?? false);
                if ($value !== self::defaultAllows($role, $key)) {
                    $rows[] = ['role' => $role, 'permission' => $key, 'allowed' => $value, 'updated_by' => $by->id, 'created_at' => now(), 'updated_at' => now()];
                }
                if ($value !== $before[$key][$role]) {
                    $changes[] = ($value ? '+' : '-') . ' ' . self::roles()[$role] . ': ' . $meta['label'];
                }
            }
        }

        DB::transaction(function () use ($rows) {
            DB::table('role_permissions')->delete();
            if ($rows) {
                DB::table('role_permissions')->insert($rows);
            }
        });
        self::flush();
        self::audit($by, $changes ? 'Ubah Hak Akses: ' . implode('; ', $changes) : 'Simpan Hak Akses (tanpa perubahan)');

        return $changes;
    }

    /** Kembalikan semua ke bawaan. */
    public static function reset(User $by): void
    {
        DB::table('role_permissions')->delete();
        self::flush();
        self::audit($by, 'Hak Akses dikembalikan ke bawaan');
    }

    public static function flush(): void
    {
        self::$overrides = null;
        Cache::forget(self::CACHE_KEY);
    }

    /** @return array<string, array<string, bool>> */
    private static function overrides(): array
    {
        if (self::$overrides !== null) {
            return self::$overrides;
        }

        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached)) {
            return self::$overrides = $cached;
        }

        // Sebelum migrasi dijalankan (mis. saat deploy) pakai nilai bawaan, dan jangan di-cache.
        if (! Schema::hasTable('role_permissions')) {
            return self::$overrides = [];
        }

        $out = [];
        foreach (DB::table('role_permissions')->get(['role', 'permission', 'allowed']) as $row) {
            $out[$row->permission][$row->role] = (bool) $row->allowed;
        }
        Cache::forever(self::CACHE_KEY, $out);

        return self::$overrides = $out;
    }

    private static function audit(User $by, string $label): void
    {
        ActivityLog::create([
            'user_id'    => $by->id,
            'route_name' => 'admin.hak-akses.update',
            'page_label' => mb_strimwidth($label, 0, 250, '…'),
            'page_group' => 'Admin',
            'url'        => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
