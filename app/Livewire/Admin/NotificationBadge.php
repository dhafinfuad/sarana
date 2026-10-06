<?php 

namespace App\Livewire\Admin; 

use App\Models\Booking; 
use App\Models\Permisi;
use App\Models\BonRequest;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component; 
use Livewire\Attributes\On; 
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;

class NotificationBadge extends Component 
{ 
    public int $pendingCount = 0; 
    public int $peminjamanCount = 0;
    public int $permisiCount = 0;
    public int $bonAtkCount = 0;

    public function mount() 
    { 
        $this->updateCount(); 
    } 
    
    #[On('booking-created')] 
    #[On('booking-updated')] 
    #[On('permisi-created')]
    #[On('permisi-updated')]
    #[On('bon-created')]
    #[On('bon-updated')]
    public function updateCount() 
    { 
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $this->peminjamanCount = 0;
        $this->permisiCount = 0;
        $this->bonAtkCount = 0;

        // 1. Peminjaman
        if ($user->isAdministrator() || $user->isSubbagUmum() || $user->nip_pendek === '060092694') {
            $this->peminjamanCount = Booking::where('status', 'pending')->count();
        } elseif (!$user->isKepalaKantor()) {
            $this->peminjamanCount = Booking::where('user_id', $user->id)->where('status', 'pending')->count();
        }

        // 2. Bon ATK
        if ($user->isAdministrator() || $user->isSubbagUmum() || $user->nip_pendek === '060092694') {
            $this->bonAtkCount = BonRequest::where('status', 'menunggu')->count();
        } elseif (!$user->isKepalaKantor()) {
            $this->bonAtkCount = BonRequest::where('user_id', $user->id)->where('status', 'menunggu')->count();
        }

        // 3. Permisi
        if ($user->canAccessPersetujuanPermisi()) {
            $query = Permisi::where('status', 'pending');
            if ($user->isAdministrator() || $user->isSubbagUmum() || $user->nip_pendek === '060092694') {
                // Subbag Umum & Admin melihat semua data permohonan tanpa filter
            } elseif ($user->isKepalaKantor()) {
                $query->whereHas('user', function (Builder $q) use ($user) {
                    $q->where(function (Builder $sub) use ($user) {
                        $sub->whereIn('jabatan', ['Kepala Seksi', 'Supervisor']);
                        if ($user->jabatan === 'Kepala Seksi') {
                            $managedSeksi = $user->getManagedSeksiList();
                            $sub->orWhere(function (Builder $sub2) use ($managedSeksi) {
                                $sub2->whereIn('seksi', $managedSeksi)
                                     ->where('jabatan', '!=', 'Kepala Seksi');
                            });
                        }
                    })->where('id', '!=', $user->id);
                });
            } elseif ($user->isSupervisor()) {
                $query->whereHas('user', function (Builder $q) use ($user) {
                    $q->where('tim', $user->tim)->where('jabatan', 'FPP')->where('id', '!=', $user->id);
                });
            } elseif ($user->isKepalaSeksi()) {
                $managedSeksi = $user->getManagedSeksiList();
                $query->whereHas('user', function (Builder $q) use ($managedSeksi, $user) {
                    $q->whereIn('seksi', $managedSeksi)->where('jabatan', '!=', 'Kepala Seksi')->where('id', '!=', $user->id);
                });
            }
            $this->permisiCount = $query->count();
        } else {
            $this->permisiCount = Permisi::where('user_id', $user->id)->where('status', 'pending')->count();
        }

        $this->pendingCount = $this->peminjamanCount + $this->permisiCount + $this->bonAtkCount;
    } 

    public function getNotifications()
    {
        $notifications = collect();
        $user = Auth::user();
        if (!$user) {
            return $notifications;
        }

        // 1. Peminjaman
        if ($user->isAdministrator() || $user->isSubbagUmum() || $user->nip_pendek === '060092694') {
            $bookings = Booking::with(['user', 'vehicle'])->where('status', 'pending')->latest()->get();
        } elseif (!$user->isKepalaKantor()) {
            $bookings = Booking::with(['user', 'vehicle'])->where('user_id', $user->id)->where('status', 'pending')->latest()->get();
        } else {
            $bookings = collect();
        }

        foreach ($bookings as $b) {
            $vehicleName = $b->vehicle->nama_kendaraan ?? 'Kendaraan Dinas';
            $userName = $b->user->name ?? 'Pegawai';
            $dest = $b->kota ? ' ke ' . $b->kota : '';
            $desc = "{$userName} mengajukan {$vehicleName}{$dest}" . ($b->keperluan ? ' untuk ' . Str::limit($b->keperluan, 45) : '.');
            
            $notifications->push([
                'id' => 'booking-' . $b->id,
                'type' => 'sarana',
                'title' => 'Peminjaman ' . $vehicleName,
                'user_name' => $userName,
                'description' => $desc,
                'module' => 'Sarana Armada',
                'badge' => 'Armada',
                'badge_color' => 'bg-blue-50 text-blue-700 border border-blue-200/60',
                'url' => $user->canAccessAdminDashboard() ? route('admin.dashboard') : route('peminjaman.index'),
                'created_at' => $b->created_at,
            ]);
        }

        // 2. Permisi
        if ($user->canAccessPersetujuanPermisi()) {
            $query = Permisi::where('status', 'pending');
            if ($user->isAdministrator() || $user->isSubbagUmum() || $user->nip_pendek === '060092694') {
                // Subbag Umum & Admin melihat semua notifikasi permohonan izin tanpa filter
            } elseif ($user->isKepalaKantor()) {
                $query->whereHas('user', function (Builder $q) use ($user) {
                    $q->where(function (Builder $sub) use ($user) {
                        $sub->whereIn('jabatan', ['Kepala Seksi', 'Supervisor']);
                        if ($user->jabatan === 'Kepala Seksi') {
                            $managedSeksi = $user->getManagedSeksiList();
                            $sub->orWhere(function (Builder $sub2) use ($managedSeksi) {
                                $sub2->whereIn('seksi', $managedSeksi)
                                     ->where('jabatan', '!=', 'Kepala Seksi');
                            });
                        }
                    })->where('id', '!=', $user->id);
                });
            } elseif ($user->isSupervisor()) {
                $query->whereHas('user', function (Builder $q) use ($user) {
                    $q->where('tim', $user->tim)->where('jabatan', 'FPP')->where('id', '!=', $user->id);
                });
            } elseif ($user->isKepalaSeksi()) {
                $managedSeksi = $user->getManagedSeksiList();
                $query->whereHas('user', function (Builder $q) use ($managedSeksi, $user) {
                    $q->whereIn('seksi', $managedSeksi)->where('jabatan', '!=', 'Kepala Seksi')->where('id', '!=', $user->id);
                });
            }
            $permisis = $query->with('user')->latest()->get();
        } else {
            $permisis = Permisi::with('user')->where('user_id', $user->id)->where('status', 'pending')->latest()->get();
        }

        foreach ($permisis as $p) {
            $userName = $p->user->name ?? 'Pegawai';
            $seksi = $p->user->seksi ? ' (' . $p->user->seksi . ')' : '';
            $desc = "{$userName}{$seksi} mengajukan izin keluar" . ($p->keperluan ? ': ' . Str::limit($p->keperluan, 45) : '.');

            $notifications->push([
                'id' => 'permisi-' . $p->id,
                'type' => 'permisi',
                'title' => 'Izin: ' . $userName,
                'user_name' => $userName,
                'description' => $desc,
                'module' => 'Permisi',
                'badge' => 'Izin Keluar',
                'badge_color' => 'bg-purple-50 text-purple-700 border border-purple-200/60',
                'url' => $user->canAccessPersetujuanPermisi() ? route('permisi.persetujuan') : route('permisi.permohonan'),
                'created_at' => $p->created_at,
            ]);
        }

        // 3. Bon ATK
        if ($user->isAdministrator() || $user->isSubbagUmum() || $user->nip_pendek === '060092694') {
            $bons = BonRequest::with('user')->where('status', 'menunggu')->latest()->get();
        } elseif (!$user->isKepalaKantor()) {
            $bons = BonRequest::with('user')->where('user_id', $user->id)->where('status', 'menunggu')->latest()->get();
        } else {
            $bons = collect();
        }

        foreach ($bons as $bon) {
            $userName = $bon->user->name ?? 'Pemohon';
            $desc = "{$userName} mengajukan permintaan ATK" . ($bon->keperluan ? ' untuk ' . Str::limit($bon->keperluan, 40) : '.');

            $notifications->push([
                'id' => 'bon-' . $bon->id,
                'type' => 'bona',
                'title' => 'Permintaan ' . $bon->no_bon,
                'user_name' => $userName,
                'description' => $desc,
                'module' => 'Bon ATK',
                'badge' => 'Bon ATK',
                'badge_color' => 'bg-amber-50 text-amber-700 border border-amber-200/60',
                'url' => ($user->isAdministrator() || $user->isSubbagUmum()) ? route('bona.kelola') : route('bona.permohonan'),
                'created_at' => $bon->created_at,
            ]);
        }

        return $notifications->sortByDesc('created_at')->values();
    }
    
    public function render() 
    { 
        return view('livewire.admin.notification-badge', [
            'notifications' => $this->getNotifications(),
        ]); 
    } 
}
