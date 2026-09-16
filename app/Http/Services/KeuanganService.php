<?php

namespace App\Http\Services;

use App\Models\Keuangan;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Auth;

class KeuanganService
{
    public function __construct(protected ActivityLogService $activityLogger) {}

    public function getKeuangan(array $filters = [], string $jenis = null)
    {
        $query = Keuangan::with('creator');

        if ($jenis) {
            $query->where('jenis', $jenis);
        } elseif (!empty($filters['jenis'])) {
            $query->where('jenis', $filters['jenis']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('kategori','like',"%{$search}%")
                  ->orWhere('keterangan','like',"%{$search}%")
                  ->orWhere('deskripsi','like',"%{$search}%");
            });
        }
        if (!empty($filters['kategori'])) {
            $query->where('kategori', $filters['kategori']);
        }
        if (!empty($filters['date_from'])) {
            $query->where('tanggal','>=',$filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('tanggal','<=',$filters['date_to']);
        }

        $perPage = in_array((int)($filters['per_page'] ?? 10), [5,10,25,50,100]) ? (int)($filters['per_page'] ?? 10) : 10;

        return $query->orderBy('tanggal','desc')->latest()->paginate($perPage)->withQueryString();
    }

    public function createKeuangan(array $data)
    {
        $data['created_by'] = Auth::id();
        $keuangan = Keuangan::create($data);
        $this->activityLogger->log($keuangan, 'created', $keuangan->toArray(), ucfirst(strtolower($keuangan->jenis)) . " {$keuangan->kategori} Rp ".number_format((float)$keuangan->jumlah,0,',','.'));
        return $keuangan;
    }

    public function updateKeuangan(Keuangan $keuangan, array $data)
    {
        $oldData = $keuangan->toArray();
        $keuangan->update($data);
        $keuangan->refresh();
        $newData = $keuangan->toArray();
        $this->activityLogger->logUpdated($keuangan, $oldData, $newData);
        return $keuangan;
    }

    public function deleteKeuangan(Keuangan $keuangan)
    {
        $keuangan->loadMissing('creator');
        $this->activityLogger->logDeleted($keuangan);
        return $keuangan->delete();
    }

    public function getSaldo(): array
    {
        $pemasukan = Keuangan::where('jenis','PEMASUKAN')->sum('jumlah');
        $pengeluaran = Keuangan::where('jenis','PENGELUARAN')->sum('jumlah');
        return [
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'saldo' => $pemasukan - $pengeluaran,
        ];
    }

    public function getLaporan(array $filters = []): array
    {
        $query = Keuangan::query();
        if (!empty($filters['date_from'])) $query->where('tanggal','>=',$filters['date_from']);
        if (!empty($filters['date_to'])) $query->where('tanggal','<=',$filters['date_to']);

        $pemasukan = (clone $query)->where('jenis','PEMASUKAN');
        $pengeluaran = (clone $query)->where('jenis','PENGELUARAN');

        $totalPemasukan = $pemasukan->sum('jumlah');
        $totalPengeluaran = $pengeluaran->sum('jumlah');

        $byKategoriPemasukan = (clone $pemasukan)->selectRaw('kategori, SUM(jumlah) as total')->groupBy('kategori')->get();
        $byKategoriPengeluaran = (clone $pengeluaran)->selectRaw('kategori, SUM(jumlah) as total')->groupBy('kategori')->get();

        // monthly trend last 6 months
        $monthly = [];
        for ($i=5; $i>=0; $i--) {
            $start = now()->subMonths($i)->startOfMonth()->toDateString();
            $end = now()->subMonths($i)->endOfMonth()->toDateString();
            $monthly[] = [
                'label' => now()->subMonths($i)->translatedFormat('M Y'),
                'pemasukan' => Keuangan::where('jenis','PEMASUKAN')->whereBetween('tanggal',[$start,$end])->sum('jumlah'),
                'pengeluaran' => Keuangan::where('jenis','PENGELUARAN')->whereBetween('tanggal',[$start,$end])->sum('jumlah'),
            ];
        }

        $list = (clone $query)->with('creator')->orderBy('tanggal','desc')->get();

        return [
            'total_pemasukan' => $totalPemasukan,
            'total_pengeluaran' => $totalPengeluaran,
            'saldo' => $totalPemasukan - $totalPengeluaran,
            'by_kategori_pemasukan' => $byKategoriPemasukan,
            'by_kategori_pengeluaran' => $byKategoriPengeluaran,
            'monthly' => $monthly,
            'list' => $list,
        ];
    }

    public function getCategories(string $jenis): array
    {
        // default categories if no data yet
        $defaults = $jenis === 'PEMASUKAN'
            ? ['Iuran Warga','Donasi','Kas RT','Bantuan Pemerintah','Usaha RT','Lain-lain']
            : ['Konsumsi','Perbaikan','Kebersihan','Keamanan','Acara','ATK','Lain-lain'];
        $existing = Keuangan::where('jenis',$jenis)->distinct()->pluck('kategori')->toArray();
        return collect(array_merge($defaults, $existing))->unique()->values()->toArray();
    }
}
