<?php

namespace App\Http\Services;

use App\Models\KartuKeluarga;
use App\Models\KasJenis;
use App\Models\KasPembayaran;
use App\Models\Warga;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasWargaService
{
    public function __construct(protected ActivityLogService $activityLogger) {}

    // ------------------------------------------------------------------
    // Master Jenis Kas
    // ------------------------------------------------------------------

    public function getJenis(array $filters = [])
    {
        $query = KasJenis::with(['rt'])
            ->withCount('pembayaran as pembayaran_count')
            ->withSum(['pembayaran as total_terkumpul' => fn ($q) => $q->where('status', 'sudah_bayar')], 'nominal_bayar');

        if ($user = Auth::user()) {
            if (!$user->isSuperAdmin() && $user->rt_id) {
                $query->where('kas_jenis.rt_id', $user->rt_id);
            } elseif (!empty($filters['rt_id'])) {
                $query->where('kas_jenis.rt_id', $filters['rt_id']);
            }
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('nama', 'like', "%{$search}%");
        }
        if (!empty($filters['periode_type'])) {
            $query->where('periode_type', $filters['periode_type']);
        }
        if (!empty($filters['target_type'])) {
            $query->where('target_type', $filters['target_type']);
        }
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        $perPage = in_array((int) ($filters['per_page'] ?? 10), [5, 10, 25, 50, 100]) ? (int) ($filters['per_page'] ?? 10) : 10;

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function createJenis(array $data): KasJenis
    {
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            $data['rt_id'] = $user->rt_id;
        }
        if (empty($data['rt_id'])) {
            abort(422, 'RT harus dipilih.');
        }
        $data['created_by'] = $user?->id;
        $jenis = KasJenis::create($data);
        $this->activityLogger->log($jenis, 'created', $jenis->toArray(), "Jenis kas {$jenis->nama} Rp " . number_format((float) $jenis->nominal, 0, ',', '.'));
        return $jenis;
    }

    public function updateJenis(KasJenis $jenis, array $data): KasJenis
    {
        $this->assertJenisAccess($jenis);
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id) {
            $data['rt_id'] = $user->rt_id;
        }
        $old = $jenis->toArray();
        $jenis->update($data);
        $jenis->refresh();
        $this->activityLogger->logUpdated($jenis, $old, $jenis->toArray());
        return $jenis;
    }

    public function deleteJenis(KasJenis $jenis): bool
    {
        $this->assertJenisAccess($jenis);
        $this->activityLogger->logDeleted($jenis);
        return $jenis->delete();
    }

    public function assertJenisAccess(KasJenis $jenis): void
    {
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->rt_id && (int) $jenis->rt_id !== (int) $user->rt_id) {
            abort(403, 'Akses jenis kas ditolak.');
        }
    }

    // ------------------------------------------------------------------
    // Ledger matriks: baris = KK / Warga, kolom = tanggal
    // ------------------------------------------------------------------

    public function defaultMode(KasJenis $jenis): string
    {
        return match ($jenis->periode_type) {
            'weekly' => 'minggu',
            'yearly' => 'tahun',
            default => 'bulan',
        };
    }

    /**
     * @return array{mode:string, columns:array, rows:\Illuminate\Contracts\Pagination\LengthAwarePaginator, payMap:array, rangeStart:Carbon, rangeEnd:Carbon, stats:array, nav:array}
     */
    public function getLedger(KasJenis $jenis, array $params = []): array
    {
        $this->assertJenisAccess($jenis);

        $mode = $params['mode'] ?? $this->defaultMode($jenis);
        if (!in_array($mode, ['bulan', 'minggu', 'tahun'], true)) {
            $mode = $this->defaultMode($jenis);
        }

        $columns = [];
        $nav = [];

        if ($mode === 'bulan') {
            $bulan = $params['bulan'] ?? Carbon::now()->format('Y-m');
            try {
                $base = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            } catch (\Throwable) {
                $base = Carbon::now()->startOfMonth();
                $bulan = $base->format('Y-m');
            }
            $days = $base->daysInMonth;
            for ($d = 1; $d <= $days; $d++) {
                $date = $base->copy()->day($d);
                $columns[] = [
                    'key' => $date->format('Y-m-d'),
                    'label' => (string) $d,
                    'sub' => $date->locale('id')->isoFormat('ddd'),
                    'full' => $date->locale('id')->isoFormat('dddd, D MMM YYYY'),
                    'is_today' => $date->isToday(),
                    'is_weekend' => $date->isWeekend(),
                ];
            }
            $rangeStart = $base->copy()->startOfDay();
            $rangeEnd = $base->copy()->endOfMonth()->endOfDay();
            $nav = [
                'prev' => $base->copy()->subMonth()->format('Y-m'),
                'next' => $base->copy()->addMonth()->format('Y-m'),
                'current' => $bulan,
                'title' => $base->locale('id')->isoFormat('MMMM YYYY'),
            ];
        } elseif ($mode === 'minggu') {
            $ref = $params['minggu'] ?? Carbon::now()->format('Y-m-d');
            try {
                $base = Carbon::parse($ref);
            } catch (\Throwable) {
                $base = Carbon::now();
            }
            $monday = $base->copy()->startOfWeek(Carbon::MONDAY);
            for ($i = 0; $i < 7; $i++) {
                $date = $monday->copy()->addDays($i);
                $columns[] = [
                    'key' => $date->format('Y-m-d'),
                    'label' => $date->format('d/m'),
                    'sub' => $date->locale('id')->isoFormat('ddd'),
                    'full' => $date->locale('id')->isoFormat('dddd, D MMM YYYY'),
                    'is_today' => $date->isToday(),
                    'is_weekend' => $date->isWeekend(),
                ];
            }
            $rangeStart = $monday->copy()->startOfDay();
            $rangeEnd = $monday->copy()->addDays(6)->endOfDay();
            $nav = [
                'prev' => $monday->copy()->subWeek()->format('Y-m-d'),
                'next' => $monday->copy()->addWeek()->format('Y-m-d'),
                'current' => $monday->format('Y-m-d'),
                'title' => 'Minggu ' . $monday->format('W') . ' • ' . $monday->locale('id')->isoFormat('D MMM') . ' – ' . $monday->copy()->addDays(6)->locale('id')->isoFormat('D MMM YYYY'),
            ];
        } else {
            $tahun = (int) ($params['tahun'] ?? Carbon::now()->year);
            if ($tahun < 2000 || $tahun > 2100) {
                $tahun = (int) Carbon::now()->year;
            }
            // Senin minggu ISO pertama tahun tsb
            $cursor = Carbon::create($tahun, 1, 4)->startOfWeek(Carbon::MONDAY);
            $endYear = Carbon::create($tahun, 12, 28)->endOfWeek(Carbon::SUNDAY);
            while ($cursor->lte($endYear)) {
                $weekKey = $cursor->format('o-\WW');
                $columns[] = [
                    'key' => $cursor->format('Y-m-d'), // Senin minggu tsb = tanggal simpan
                    'week' => $weekKey,
                    'label' => 'W' . $cursor->format('W'),
                    'sub' => $cursor->format('d/m'),
                    'full' => 'Minggu ' . $cursor->format('W') . ' • ' . $cursor->locale('id')->isoFormat('D MMM') . ' – ' . $cursor->copy()->addDays(6)->locale('id')->isoFormat('D MMM YYYY'),
                    'is_today' => Carbon::now()->between($cursor, $cursor->copy()->addDays(6)),
                    'is_weekend' => false,
                ];
                $cursor->addWeek();
            }
            $rangeStart = Carbon::create($tahun, 1, 1)->startOfDay();
            $rangeEnd = Carbon::create($tahun, 12, 31)->endOfDay();
            $nav = [
                'prev' => $tahun - 1,
                'next' => $tahun + 1,
                'current' => $tahun,
                'title' => 'Tahun ' . $tahun . ' • ' . count($columns) . ' minggu',
            ];
        }

        // ---- Baris subjek ----
        $search = $params['search'] ?? null;
        $perPage = in_array((int) ($params['per_page'] ?? 25), [10, 25, 50, 100]) ? (int) ($params['per_page'] ?? 25) : 25;

        if ($jenis->isKk()) {
            $query = KartuKeluarga::with('anggota')->where('rt_id', $jenis->rt_id);
            if ($search) {
                $query->where(fn ($q) => $q->where('kepala_keluarga', 'like', "%{$search}%")->orWhere('no_kk', 'like', "%{$search}%"));
            }
            $rows = $query->orderBy('kepala_keluarga')->paginate($perPage)->withQueryString();
            $subjectIds = $rows->getCollection()->pluck('id')->all();
            $totalSubjek = KartuKeluarga::where('rt_id', $jenis->rt_id)->count();
        } else {
            $query = Warga::where('rt_id', $jenis->rt_id)->where('status_warga', 'AKTIF');
            if ($search) {
                $query->where(fn ($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%"));
            }
            $rows = $query->orderBy('nama')->paginate($perPage)->withQueryString();
            $subjectIds = $rows->getCollection()->pluck('id')->all();
            $totalSubjek = Warga::where('rt_id', $jenis->rt_id)->where('status_warga', 'AKTIF')->count();
        }

        // ---- Pembayaran dalam rentang ----
        $payQuery = KasPembayaran::where('kas_jenis_id', $jenis->id)
            ->where('status', 'sudah_bayar')
            ->whereBetween('tanggal', [$rangeStart->toDateString(), $rangeEnd->toDateString()]);
        if ($jenis->isKk()) {
            $payQuery->whereIn('kartu_keluarga_id', $subjectIds);
        } else {
            $payQuery->whereIn('warga_id', $subjectIds);
        }
        $payments = $payQuery->get();

        $payMap = [];
        foreach ($payments as $p) {
            $sid = $jenis->isKk() ? $p->kartu_keluarga_id : $p->warga_id;
            $cellKey = $mode === 'tahun'
                ? $p->tanggal->format('o-\WW')
                : $p->tanggal->format('Y-m-d');
            $payMap[$sid . '|' . $cellKey] = $p;
        }

        $selTerbayar = count($payMap);
        $totalRupiah = (float) $payments->sum('nominal_bayar');

        // Total sel periode ini (semua subjek x semua kolom) untuk progres
        $totalSel = $totalSubjek * max(count($columns), 1);
        // Hitung global terbayar dalam rentang (semua halaman) untuk progres akurat
        $globalCount = KasPembayaran::where('kas_jenis_id', $jenis->id)
            ->where('status', 'sudah_bayar')
            ->whereBetween('tanggal', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->count();
        $globalRupiah = (float) KasPembayaran::where('kas_jenis_id', $jenis->id)
            ->where('status', 'sudah_bayar')
            ->whereBetween('tanggal', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->sum('nominal_bayar');

        return [
            'mode' => $mode,
            'columns' => $columns,
            'rows' => $rows,
            'payMap' => $payMap,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'stats' => [
                'total_subjek' => $totalSubjek,
                'total_kolom' => count($columns),
                'total_sel' => $totalSel,
                'terbayar' => $globalCount,
                'belum' => max($totalSel - $globalCount, 0),
                'rupiah' => $globalRupiah,
                'rupiah_halaman' => $totalRupiah,
                'persen' => $totalSel > 0 ? round($globalCount / $totalSel * 100, 1) : 0,
            ],
            'nav' => $nav,
        ];
    }

    // ------------------------------------------------------------------
    // Bayar / Batal
    // ------------------------------------------------------------------

    public function bayar(KasJenis $jenis, array $data): KasPembayaran
    {
        $this->assertJenisAccess($jenis);
        $user = Auth::user();

        $tanggal = Carbon::parse($data['tanggal'])->toDateString();

        if ($jenis->isKk()) {
            if (empty($data['kartu_keluarga_id'])) {
                abort(422, 'KK harus dipilih.');
            }
            $kk = KartuKeluarga::findOrFail($data['kartu_keluarga_id']);
            if ((int) $kk->rt_id !== (int) $jenis->rt_id) {
                abort(422, 'KK bukan dari RT jenis kas ini.');
            }
            $lookup = ['kartu_keluarga_id' => $kk->id, 'warga_id' => null];
        } else {
            if (empty($data['warga_id'])) {
                abort(422, 'Warga harus dipilih.');
            }
            $warga = Warga::findOrFail($data['warga_id']);
            if ((int) $warga->rt_id !== (int) $jenis->rt_id) {
                abort(422, 'Warga bukan dari RT jenis kas ini.');
            }
            $lookup = ['warga_id' => $warga->id, 'kartu_keluarga_id' => null];
        }

        return DB::transaction(function () use ($jenis, $lookup, $tanggal, $data, $user) {
            $existing = KasPembayaran::where('kas_jenis_id', $jenis->id)
                ->where('tanggal', $tanggal)
                ->where('kartu_keluarga_id', $lookup['kartu_keluarga_id'])
                ->where('warga_id', $lookup['warga_id'])
                ->lockForUpdate()
                ->first();

            $payload = [
                'nominal_bayar' => $data['nominal_bayar'],
                'waktu_bayar' => Carbon::now(),
                'status' => 'sudah_bayar',
                'catatan' => $data['catatan'] ?? null,
                'created_by' => $user?->id,
            ];

            if ($existing) {
                $old = $existing->toArray();
                $existing->update($payload);
                $existing->refresh();
                $this->activityLogger->logUpdated($existing, $old, $existing->toArray());
                return $existing;
            }

            $bayar = KasPembayaran::create(array_merge([
                'kas_jenis_id' => $jenis->id,
                'tanggal' => $tanggal,
            ], $lookup, $payload));
            $this->activityLogger->log($bayar, 'created', $bayar->toArray(), "Bayar {$jenis->nama} {$tanggal} Rp " . number_format((float) $bayar->nominal_bayar, 0, ',', '.'));

            return $bayar;
        });
    }

    public function batalBayar(KasPembayaran $pembayaran): void
    {
        $pembayaran->loadMissing('jenis');
        if ($pembayaran->jenis) {
            $this->assertJenisAccess($pembayaran->jenis);
        }
        $this->activityLogger->logDeleted($pembayaran);
        $pembayaran->delete();
    }
}
