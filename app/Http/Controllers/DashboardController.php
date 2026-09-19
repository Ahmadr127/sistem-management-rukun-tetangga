<?php

namespace App\Http\Controllers;

use App\Models\OrganizationUnit;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\KartuKeluarga;
use App\Models\Warga;
use App\Models\Keuangan;
use App\Models\Inventaris;
use App\Models\PeminjamanInventaris;
use App\Models\MutasiWarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isSuper = $user->isSuperAdmin();

        $keuanganQuery = Keuangan::query();
        $wargaQuery = Warga::query();
        $kkQuery = KartuKeluarga::query();
        $mutasiQuery = MutasiWarga::query();
        if (!$isSuper && $user->rt_id) {
            $keuanganQuery->where('rt_id', $user->rt_id);
            $wargaQuery->where('rt_id', $user->rt_id);
            $kkQuery->where('rt_id', $user->rt_id);
            // mutasi via warga rt
            $mutasiQuery->whereHas('warga', fn($q)=> $q->where('rt_id', $user->rt_id));
        }

        $saldo = [
            'pemasukan' => (clone $keuanganQuery)->where('jenis','PEMASUKAN')->sum('jumlah'),
            'pengeluaran' => (clone $keuanganQuery)->where('jenis','PENGELUARAN')->sum('jumlah'),
        ];
        $saldo['saldo'] = $saldo['pemasukan'] - $saldo['pengeluaran'];

        // Kas warga stats
        $kasQuery = \App\Models\KasWarga::query();
        if (!$isSuper && $user->rt_id) $kasQuery->where('rt_id', $user->rt_id);
        $kasBelum = (clone $kasQuery)->where('status','belum_bayar')->count();
        $kasLunas = (clone $kasQuery)->where('status','sudah_bayar')->count();

        $stats = [
            ['label' => $isSuper ? 'Warga' : 'Warga RT Saya', 'value' => $wargaQuery->count(), 'icon' => 'bi-people-fill', 'color' => 'bg-teal-600'],
            ['label' => $isSuper ? 'Kartu Keluarga' : 'KK RT Saya', 'value' => $kkQuery->count(), 'icon' => 'bi-house-heart-fill', 'color' => 'bg-blue-500'],
            ['label' => 'Saldo Kas', 'value' => 'Rp '.number_format($saldo['saldo'],0,',','.'), 'icon' => 'bi-cash-coin', 'color' => 'bg-emerald-500'],
            ['label' => 'Inventaris', 'value' => Inventaris::count(), 'icon' => 'bi-box-seam-fill', 'color' => 'bg-amber-500'],
            ['label' => 'Mutasi (bulan ini)', 'value' => (clone $mutasiQuery)->whereMonth('tanggal_mutasi', now()->month)->whereYear('tanggal_mutasi', now()->year)->count(), 'icon' => 'bi-arrow-left-right', 'color' => 'bg-purple-500'],
            ['label' => 'Peminjaman Aktif', 'value' => PeminjamanInventaris::where('status','DIPINJAM')->count(), 'icon' => 'bi-box-arrow-right', 'color' => 'bg-rose-500'],
            ['label' => $isSuper ? 'RT Terdata' : 'RT Saya', 'value' => $isSuper ? \App\Models\Rt::count() : 1, 'icon' => 'bi-geo-alt-fill', 'color' => 'bg-teal-500'],
            ['label' => 'Kas Belum/Lunas', 'value' => $kasBelum.' / '.$kasLunas, 'icon' => 'bi-wallet2', 'color' => 'bg-green-600'],
        ];
        if (!$isSuper) {
            // Replace Pengguna stat with kas info for RT users
            $stats[6] = ['label' => 'RT Saya', 'value' => $user->rt?->kode_rt ?? '-', 'icon' => 'bi-geo-alt-fill', 'color' => 'bg-teal-500'];
        }

        // Chart data: warga baru vs mutasi per bulan (6 bulan terakhir) - scoped
        $chartLabels = [];
        $wargaChart = [];
        $mutasiChart = [];
        $keuanganPemasukan = [];
        $keuanganPengeluaran = [];
        for ($i = 5; $i >= 0; $i--) {
            $chartLabels[] = now()->subMonths($i)->translatedFormat('M Y');
            $wQ = Warga::whereBetween('created_at', [now()->subMonths($i)->startOfMonth(), now()->subMonths($i)->endOfMonth()]);
            if (!$isSuper && $user->rt_id) $wQ->where('rt_id', $user->rt_id);
            $wargaChart[] = $wQ->count();

            $mQ = MutasiWarga::whereBetween('tanggal_mutasi', [now()->subMonths($i)->startOfMonth()->toDateString(), now()->subMonths($i)->endOfMonth()->toDateString()]);
            if (!$isSuper && $user->rt_id) $mQ->whereHas('warga', fn($q)=> $q->where('rt_id', $user->rt_id));
            $mutasiChart[] = $mQ->count();

            $kP = Keuangan::where('jenis','PEMASUKAN')->whereBetween('tanggal', [now()->subMonths($i)->startOfMonth()->toDateString(), now()->subMonths($i)->endOfMonth()->toDateString()]);
            $kK = Keuangan::where('jenis','PENGELUARAN')->whereBetween('tanggal', [now()->subMonths($i)->startOfMonth()->toDateString(), now()->subMonths($i)->endOfMonth()->toDateString()]);
            if (!$isSuper && $user->rt_id) { $kP->where('rt_id', $user->rt_id); $kK->where('rt_id', $user->rt_id); }
            $keuanganPemasukan[] = (float) $kP->sum('jumlah');
            $keuanganPengeluaran[] = (float) $kK->sum('jumlah');
        }

        // Inventaris diagram data
        $invKategoriQ = Inventaris::selectRaw('COALESCE(kategori,"Tanpa Kategori") as kategori, SUM(jumlah) as total_unit')
            ->groupBy('kategori')->orderByDesc('total_unit')->get();
        $inventarisKategoriLabels = $invKategoriQ->pluck('kategori')->toArray();
        $inventarisKategoriData = $invKategoriQ->pluck('total_unit')->map(fn($v)=>(int)$v)->toArray();

        $invKondisiQ = Inventaris::selectRaw('kondisi, COUNT(*) as jml')->groupBy('kondisi')->get();
        $inventarisKondisiLabels = $invKondisiQ->pluck('kondisi')->toArray();
        $inventarisKondisiData = $invKondisiQ->pluck('jml')->map(fn($v)=>(int)$v)->toArray();

        // Peminjaman status for completeness (optional)
        $peminjamanStatusQ = PeminjamanInventaris::selectRaw('status, COUNT(*) as jml')->groupBy('status')->get();
        $peminjamanStatusLabels = $peminjamanStatusQ->pluck('status')->toArray();
        $peminjamanStatusData = $peminjamanStatusQ->pluck('jml')->map(fn($v)=>(int)$v)->toArray();

        // Backward compat for old view
        $chartData = $wargaChart;

        // pengelompokan data antar RT - now from rts table
        if ($isSuper) {
            $rtGrouping = \App\Models\Rt::withCount(['kartuKeluarga','warga'])->orderBy('kode_rt')->get()->map(fn($rt)=> (object)['rt'=>$rt->kode_rt,'jml_kk'=>$rt->kartu_keluarga_count,'nama'=>$rt->nama_rt,'id'=>$rt->id]);
            $rtWargaGrouping = \App\Models\Rt::withCount('warga')->orderBy('kode_rt')->get()->map(fn($rt)=> (object)['rt'=>$rt->kode_rt,'jml'=>$rt->warga_count]);
        } else {
            $rt = $user->rt;
            $rtGrouping = collect([(object)['rt'=>$rt->kode_rt,'jml_kk'=>KartuKeluarga::where('rt_id',$rt->id)->count(),'nama'=>$rt->nama_rt]]);
            $rtWargaGrouping = collect([(object)['rt'=>$rt->kode_rt,'jml'=>Warga::where('rt_id',$rt->id)->count()]]);
        }

        // Data untuk tabel dengan pencarian per kolom
        $tableRows = User::with('role')->limit(50)->get()->map(fn($u) => [
            'name' => $u->name,
            'nik' => $u->nik ?? '-',
            'username' => $u->username,
            'email' => $u->email,
            'role' => $u->role->display_name ?? '-',
            'created_at' => $u->created_at->format('d/m/Y'),
        ])->toArray();

        $wargaPerRt = $rtWargaGrouping;
        $kkPerRt = $rtGrouping;

        return view('dashboard', compact('user', 'stats', 'chartLabels', 'chartData', 'wargaChart', 'mutasiChart', 'keuanganPemasukan', 'keuanganPengeluaran', 'tableRows', 'rtGrouping', 'rtWargaGrouping', 'wargaPerRt', 'kkPerRt', 'saldo','inventarisKategoriLabels','inventarisKategoriData','inventarisKondisiLabels','inventarisKondisiData','peminjamanStatusLabels','peminjamanStatusData'));
    }
}
