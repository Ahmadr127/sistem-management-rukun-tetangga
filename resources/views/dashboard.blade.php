@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-5">
    <!-- Welcome + Quick Actions -->
    <x-card>
        <h2 class="text-2xl font-bold text-gray-900">Selamat Datang, {{ $user->name }}!</h2>
        <p class="text-gray-500 mb-4">Sistem Manajemen Rukun Tetangga — Kelola Warga, Keuangan & Inventaris</p>

        <!-- <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            @if($user->hasPermission('view_warga'))
            <a href="{{ route('warga.index') }}" class="flex items-center p-3 bg-sp-primary/5 hover:bg-sp-primary/10 rounded-lg transition-colors group">
                <div class="w-9 h-9 bg-sp-primary rounded-lg flex items-center justify-center mr-3 text-white flex-shrink-0 shadow-sm"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="font-semibold text-sp-navy">Data Warga</div>
                    <div class="text-xs text-sp-primary">Pendataan warga</div>
                </div>
            </a>
            @endif
            @if($user->hasPermission('view_kk'))
            <a href="{{ route('kartu-keluarga.index') }}" class="flex items-center p-3 bg-blue-500/5 hover:bg-blue-500/10 rounded-lg transition-colors group">
                <div class="w-9 h-9 bg-blue-500 rounded-lg flex items-center justify-center mr-3 text-white flex-shrink-0 shadow-sm"><i class="bi bi-house-heart-fill"></i></div>
                <div>
                    <div class="font-semibold text-sp-navy">Kartu Keluarga</div>
                    <div class="text-xs text-blue-500">KK & anggota</div>
                </div>
            </a>
            @endif
            @if($user->hasPermission('view_keuangan'))
            <a href="{{ route('keuangan.pemasukan.index') }}" class="flex items-center p-3 bg-green-500/5 hover:bg-green-500/10 rounded-lg transition-colors group">
                <div class="w-9 h-9 bg-green-500 rounded-lg flex items-center justify-center mr-3 text-white flex-shrink-0 shadow-sm"><i class="bi bi-cash-coin"></i></div>
                <div>
                    <div class="font-semibold text-sp-navy">Keuangan</div>
                    <div class="text-xs text-green-600">Pemasukan & Pengeluaran</div>
                </div>
            </a>
            @endif
            @if($user->hasPermission('view_inventaris'))
            <a href="{{ route('inventaris.index') }}" class="flex items-center p-3 bg-amber-500/5 hover:bg-amber-500/10 rounded-lg transition-colors group">
                <div class="w-9 h-9 bg-amber-500 rounded-lg flex items-center justify-center mr-3 text-white flex-shrink-0 shadow-sm"><i class="bi bi-box-seam-fill"></i></div>
                <div>
                    <div class="font-semibold text-sp-navy">Inventaris</div>
                    <div class="text-xs text-amber-600">Data barang</div>
                </div>
            </a>
            @endif
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
            @if($user->hasPermission('view_mutasi'))
            <a href="{{ route('mutasi-warga.index') }}" class="flex items-center p-3 bg-orange-500/5 hover:bg-orange-500/10 rounded-lg transition-colors group">
                <div class="w-9 h-9 bg-orange-500 rounded-lg flex items-center justify-center mr-3 text-white flex-shrink-0 shadow-sm"><i class="bi bi-arrow-left-right"></i></div>
                <div><div class="font-semibold text-sp-navy">Mutasi Warga</div><div class="text-xs text-orange-600">Masuk / Keluar / Lahir / Mati</div></div>
            </a>
            @endif
            @if($user->hasPermission('view_peminjaman'))
            <a href="{{ route('peminjaman-inventaris.index') }}" class="flex items-center p-3 bg-rose-500/5 hover:bg-rose-500/10 rounded-lg transition-colors group">
                <div class="w-9 h-9 bg-rose-500 rounded-lg flex items-center justify-center mr-3 text-white flex-shrink-0 shadow-sm"><i class="bi bi-box-arrow-right"></i></div>
                <div><div class="font-semibold text-sp-navy">Peminjaman</div><div class="text-xs text-rose-600">Pinjam & kembali</div></div>
            </a>
            @endif
            @if($user->hasPermission('view_laporan_keuangan'))
            <a href="{{ route('keuangan.laporan') }}" class="flex items-center p-3 bg-emerald-500/5 hover:bg-emerald-500/10 rounded-lg transition-colors group">
                <div class="w-9 h-9 bg-emerald-500 rounded-lg flex items-center justify-center mr-3 text-white flex-shrink-0 shadow-sm"><i class="bi bi-graph-up"></i></div>
                <div><div class="font-semibold text-sp-navy">Laporan Keuangan</div><div class="text-xs text-emerald-600">Rekap & laporan</div></div>
            </a>
            @endif
            @if($user->hasPermission('view_laporan_inventaris'))
            <a href="{{ route('inventaris.laporan') }}" class="flex items-center p-3 bg-teal-500/5 hover:bg-teal-500/10 rounded-lg transition-colors group">
                <div class="w-9 h-9 bg-teal-500 rounded-lg flex items-center justify-center mr-3 text-white flex-shrink-0 shadow-sm"><i class="bi bi-clipboard-data-fill"></i></div>
                <div><div class="font-semibold text-sp-navy">Laporan Inventaris</div><div class="text-xs text-teal-600">Rekap barang</div></div>
            </a>
            @endif
        </div> -->
    </x-card>

     <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach($stats as $stat)
        <x-stats :label="$stat['label']" :value="$stat['value']" :icon="$stat['icon']" :color="$stat['color']" />
        @endforeach
    </div>

    <!-- Quick Actions Warga & Keuangan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
        <x-card title="Pengelompokan Data antar RT" subtitle="Jumlah Warga & KK per RT">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="bg-gray-50"><th class="px-3 py-2 text-left">RT</th><th class="px-3 py-2 text-center">Jml KK</th><th class="px-3 py-2 text-center">Jml Warga</th></tr></thead>
                    <tbody>
                        @forelse($rtGrouping as $rt)
                        @php $w = $rtWargaGrouping->firstWhere('rt',$rt->rt); @endphp
                        <tr class="border-t"><td class="px-3 py-2 font-semibold">RT {{ $rt->rt }}</td><td class="px-3 py-2 text-center">{{ $rt->jml_kk }}</td><td class="px-3 py-2 text-center">{{ $w->jml ?? 0 }}</td></tr>
                        @empty
                        <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada data RT</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 flex gap-2">
                <a href="{{ route('warga.index') }}" class="px-3 py-1.5 bg-sp-primary text-white rounded-md text-xs">Lihat Data Warga</a>
                <a href="{{ route('kartu-keluarga.index') }}" class="px-3 py-1.5 border rounded-md text-xs">Lihat KK</a>
            </div>
        </x-card>
        <x-card title="Saldo Kas RT">
            <div class="space-y-2">
                <div class="flex justify-between text-sm"><span class="text-gray-500">Pemasukan</span><span class="font-semibold text-green-600">Rp {{ number_format($saldo['pemasukan'],0,',','.') }}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Pengeluaran</span><span class="font-semibold text-red-600">Rp {{ number_format($saldo['pengeluaran'],0,',','.') }}</span></div>
                <div class="border-t pt-2 flex justify-between font-bold"><span>Saldo</span><span class="{{ $saldo['saldo']>=0?'text-sp-primary':'text-red-600' }}">Rp {{ number_format($saldo['saldo'],0,',','.') }}</span></div>
                <div class="flex gap-2 pt-2">
                    <a href="{{ route('keuangan.pemasukan.index') }}" class="flex-1 text-center px-3 py-1.5 bg-green-600 text-white rounded-md text-xs">Pemasukan</a>
                    <a href="{{ route('keuangan.pengeluaran.index') }}" class="flex-1 text-center px-3 py-1.5 bg-red-600 text-white rounded-md text-xs">Pengeluaran</a>
                    <a href="{{ route('keuangan.laporan') }}" class="flex-1 text-center px-3 py-1.5 border rounded-md text-xs">Laporan</a>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-card title="Tren Warga & Mutasi" subtitle="6 bulan terakhir">
            <x-chart
                type="bar"
                :labels="$chartLabels"
                :datasets="[
                    ['label' => 'Warga Baru', 'data' => $wargaChart, 'backgroundColor' => '#007774'],
                    ['label' => 'Mutasi', 'data' => $mutasiChart, 'backgroundColor' => '#f59e0b'],
                ]"
                :height="260"
            />
        </x-card>
        <x-card title="Tren Keuangan" subtitle="Pemasukan vs Pengeluaran (Rp)">
            <x-chart
                type="line"
                :labels="$chartLabels"
                :datasets="[
                    ['label' => 'Pemasukan', 'data' => $keuanganPemasukan, 'borderColor' => '#16a34a', 'backgroundColor' => 'rgba(22,163,74,0.15)', 'fill' => true, 'tension' => 0.3],
                    ['label' => 'Pengeluaran', 'data' => $keuanganPengeluaran, 'borderColor' => '#dc2626', 'backgroundColor' => 'rgba(220,38,38,0.15)', 'fill' => true, 'tension' => 0.3],
                ]"
                :height="260"
            />
        </x-card>
    </div>
    <x-card title="Tren Pengguna" subtitle="Pengguna baru dalam 6 bulan terakhir">
        <x-chart
            type="line"
            :labels="$chartLabels"
            :datasets="[[
                'label' => 'Pengguna Baru',
                'data' => $chartData,
                'borderColor' => '#007774',
                'backgroundColor' => 'rgba(0, 119, 116, 0.15)',
                'fill' => true,
                'tension' => 0.3,
                'pointRadius' => 4,
                'pointBackgroundColor' => '#007774',
            ]]"
            :height="260"
        />
    </x-card>

    <!-- Searchable Table (pencarian per kolom di baris pertama) -->
    <x-card title="Data Pengguna" subtitle="Ketik di kolom pencarian untuk memfilter data">
        <x-searchable-table
            :columns="[
                ['key' => 'name', 'label' => 'Nama'],
                ['key' => 'nik', 'label' => 'NIK'],
                ['key' => 'username', 'label' => 'Username'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'role', 'label' => 'Role'],
                ['key' => 'created_at', 'label' => 'Dibuat'],
            ]"
            :rows="$tableRows"
            :per-page="8"
        />
    </x-card>
</div>
@endsection
