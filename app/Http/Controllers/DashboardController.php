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

        $saldo = [
            'pemasukan' => Keuangan::where('jenis','PEMASUKAN')->sum('jumlah'),
            'pengeluaran' => Keuangan::where('jenis','PENGELUARAN')->sum('jumlah'),
        ];
        $saldo['saldo'] = $saldo['pemasukan'] - $saldo['pengeluaran'];

        $stats = [
            ['label' => 'Warga', 'value' => Warga::count(), 'icon' => 'bi-people-fill', 'color' => 'bg-sp-primary'],
            ['label' => 'Kartu Keluarga', 'value' => KartuKeluarga::count(), 'icon' => 'bi-house-heart-fill', 'color' => 'bg-blue-500'],
            ['label' => 'Saldo Kas', 'value' => 'Rp '.number_format($saldo['saldo'],0,',','.'), 'icon' => 'bi-cash-coin', 'color' => 'bg-emerald-500'],
            ['label' => 'Inventaris', 'value' => Inventaris::count(), 'icon' => 'bi-box-seam-fill', 'color' => 'bg-amber-500'],
            ['label' => 'Mutasi (bulan ini)', 'value' => MutasiWarga::whereMonth('tanggal_mutasi', now()->month)->whereYear('tanggal_mutasi', now()->year)->count(), 'icon' => 'bi-arrow-left-right', 'color' => 'bg-purple-500'],
            ['label' => 'Peminjaman Aktif', 'value' => PeminjamanInventaris::where('status','DIPINJAM')->count(), 'icon' => 'bi-box-arrow-right', 'color' => 'bg-rose-500'],
            ['label' => 'Pengguna', 'value' => User::count(), 'icon' => 'bi-person-fill-gear', 'color' => 'bg-slate-500'],
            ['label' => 'RT Terdata', 'value' => KartuKeluarga::whereNotNull('rt')->distinct()->count('rt'), 'icon' => 'bi-geo-alt-fill', 'color' => 'bg-teal-500'],
        ];

        // Chart data: warga baru vs mutasi per bulan (6 bulan terakhir)
        $chartLabels = [];
        $wargaChart = [];
        $mutasiChart = [];
        $keuanganPemasukan = [];
        $keuanganPengeluaran = [];
        for ($i = 5; $i >= 0; $i--) {
            $chartLabels[] = now()->subMonths($i)->translatedFormat('M Y');
            $wargaChart[] = Warga::whereBetween('created_at', [
                now()->subMonths($i)->startOfMonth(),
                now()->subMonths($i)->endOfMonth(),
            ])->count();
            $mutasiChart[] = MutasiWarga::whereBetween('tanggal_mutasi', [
                now()->subMonths($i)->startOfMonth()->toDateString(),
                now()->subMonths($i)->endOfMonth()->toDateString(),
            ])->count();
            $keuanganPemasukan[] = (float) Keuangan::where('jenis','PEMASUKAN')->whereBetween('tanggal', [
                now()->subMonths($i)->startOfMonth()->toDateString(),
                now()->subMonths($i)->endOfMonth()->toDateString(),
            ])->sum('jumlah');
            $keuanganPengeluaran[] = (float) Keuangan::where('jenis','PENGELUARAN')->whereBetween('tanggal', [
                now()->subMonths($i)->startOfMonth()->toDateString(),
                now()->subMonths($i)->endOfMonth()->toDateString(),
            ])->sum('jumlah');
        }

        // Backward compat for old view
        $chartData = $wargaChart;

        // pengelompokan data antar RT
        $rtGrouping = KartuKeluarga::select('rt', DB::raw('COUNT(*) as jml_kk'))
            ->whereNotNull('rt')->groupBy('rt')->orderBy('rt')->get();
        $rtWargaGrouping = Warga::selectRaw('kartu_keluarga.rt as rt, COUNT(*) as jml')
            ->join('kartu_keluarga','kartu_keluarga.id','=','warga.kartu_keluarga_id')
            ->whereNotNull('kartu_keluarga.rt')->groupBy('kartu_keluarga.rt')->orderBy('rt')->get();

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

        return view('dashboard', compact('user', 'stats', 'chartLabels', 'chartData', 'wargaChart', 'mutasiChart', 'keuanganPemasukan', 'keuanganPengeluaran', 'tableRows', 'rtGrouping', 'rtWargaGrouping', 'wargaPerRt', 'kkPerRt', 'saldo'));
    }
}
