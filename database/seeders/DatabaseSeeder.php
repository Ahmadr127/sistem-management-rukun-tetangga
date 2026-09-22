<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call RolePermissionSeeder first
        $this->call([
            RolePermissionSeeder::class,
            RtSeeder::class,
            OrganizationTypeSeeder::class,
            OrganizationUnitSeeder::class,
        ]);

        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $rtRole = \App\Models\Role::where('name', 'rt')->first() ?? $adminRole;
        $rt1 = \App\Models\Rt::where('kode_rt','RT 01')->first();
        $rt2 = \App\Models\Rt::where('kode_rt','RT 02')->first();

        // Create superadmin user (rt_id null)
        User::factory()->create([
            'name' => 'Superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'role_id' => $adminRole->id,
            'rt_id' => null,
            'password' => bcrypt('123'),
        ]);
        // Legacy admin alias
        if (!User::where('username','admin')->exists()) {
            User::factory()->create([
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'role_id' => $adminRole->id,
                'rt_id' => null,
                'password' => bcrypt('123'),
            ]);
        }

        // Create RT users
        if ($rt1) {
            User::firstOrCreate(['username'=>'rt01'],[
                'name' => 'Pengurus RT 01',
                'email' => 'rt01@example.com',
                'role_id' => $rtRole->id,
                'rt_id' => $rt1->id,
                'password' => bcrypt('123'),
                'email_verified_at' => now(),
            ]);
        }
        if ($rt2) {
            User::firstOrCreate(['username'=>'rt02'],[
                'name' => 'Pengurus RT 02',
                'email' => 'rt02@example.com',
                'role_id' => $rtRole->id,
                'rt_id' => $rt2->id,
                'password' => bcrypt('123'),
                'email_verified_at' => now(),
            ]);
        }

        // Seed kartu keluarga & warga for demo
        $this->seedDemoKartuKeluargaWarga($rt1, $rt2);
        // Seed demo kas warga
        $this->seedDemoKasWarga();
    }

    private function seedDemoKartuKeluargaWarga($rt1, $rt2): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('kartu_keluarga') || !\Illuminate\Support\Facades\Schema::hasTable('warga')) return;
        if (\App\Models\KartuKeluarga::count() > 0) return;

        // Semua KK demo dalam 1 RT (RT 01) agar data konsisten:
        // KK.rt_id == warga.rt_id, tiap warga terikat ke KK-nya sendiri.
        $rtId = $rt1?->id;

        $kkData = [
            [
                'no_kk' => '3270010101010001', 'kepala' => 'Ahmad Hidayat',
                'alamat' => 'Jl. Mawar No. 1',
                'anggota' => [
                    ['nama' => 'Ahmad Hidayat', 'nik' => '3270010101900001', 'hubungan' => 'Kepala Keluarga', 'jk' => 'L', 'umur' => 45],
                    ['nama' => 'Siti Aminah', 'nik' => '3270010101950002', 'hubungan' => 'Istri', 'jk' => 'P', 'umur' => 42],
                    ['nama' => 'Rizky Hidayat', 'nik' => '3270010101100003', 'hubungan' => 'Anak', 'jk' => 'L', 'umur' => 18],
                ],
            ],
            [
                'no_kk' => '3270010101010002', 'kepala' => 'Budi Santoso',
                'alamat' => 'Jl. Mawar No. 2',
                'anggota' => [
                    ['nama' => 'Budi Santoso', 'nik' => '3270010101880004', 'hubungan' => 'Kepala Keluarga', 'jk' => 'L', 'umur' => 50],
                    ['nama' => 'Dewi Lestari', 'nik' => '3270010101920005', 'hubungan' => 'Istri', 'jk' => 'P', 'umur' => 47],
                ],
            ],
            [
                'no_kk' => '3270010101010003', 'kepala' => 'Citra Permata',
                'alamat' => 'Jl. Melati No. 1',
                'anggota' => [
                    ['nama' => 'Citra Permata', 'nik' => '3270010101930006', 'hubungan' => 'Kepala Keluarga', 'jk' => 'P', 'umur' => 35],
                    ['nama' => 'Eka Pratama', 'nik' => '3270010101150007', 'hubungan' => 'Anak', 'jk' => 'L', 'umur' => 12],
                    ['nama' => 'Fitriani', 'nik' => '3270010101180008', 'hubungan' => 'Anak', 'jk' => 'P', 'umur' => 8],
                ],
            ],
            [
                'no_kk' => '3270010101010004', 'kepala' => 'Deni Kurniawan',
                'alamat' => 'Jl. Melati No. 2',
                'anggota' => [
                    ['nama' => 'Deni Kurniawan', 'nik' => '3270010101850009', 'hubungan' => 'Kepala Keluarga', 'jk' => 'L', 'umur' => 55],
                    ['nama' => 'Ratna Sari', 'nik' => '3270010101890010', 'hubungan' => 'Istri', 'jk' => 'P', 'umur' => 52],
                ],
            ],
        ];

        foreach ($kkData as $d) {
            $kk = \App\Models\KartuKeluarga::create([
                'no_kk' => $d['no_kk'],
                'kepala_keluarga' => $d['kepala'],
                'alamat' => $d['alamat'],
                'rt' => '01',
                'rw' => '01',
                'rt_id' => $rtId,
                'desa' => 'Demo', 'kecamatan' => 'Demo', 'kabupaten' => 'Demo', 'provinsi' => 'Jabar',
            ]);
            foreach ($d['anggota'] as $a) {
                \App\Models\Warga::create([
                    'kartu_keluarga_id' => $kk->id,
                    'rt_id' => $rtId,
                    'nik' => $a['nik'],
                    'nama' => $a['nama'],
                    'jenis_kelamin' => $a['jk'],
                    'hubungan_keluarga' => $a['hubungan'],
                    'status_warga' => 'AKTIF',
                    'tempat_lahir' => 'Bogor',
                    'tanggal_lahir' => now()->subYears($a['umur'])->toDateString(),
                ]);
            }
        }
    }

    private function seedDemoKasWarga(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('kas_warga')) return;
        if (\App\Models\KasWarga::count() > 0) return;
        $wargas = \App\Models\Warga::with('rt')->get();
        foreach ($wargas as $w) {
            if (!$w->rt_id) continue;
            // create monthly kas for current month
            $periode = now()->format('Y-m');
            try {
                \App\Models\KasWarga::create([
                    'rt_id'=>$w->rt_id,
                    'warga_id'=>$w->id,
                    'periode_type'=>'monthly',
                    'periode'=>$periode,
                    'nominal'=>20000,
                    'status'=> $w->id %2==0 ? 'sudah_bayar' : 'belum_bayar',
                    'tanggal_bayar'=> $w->id %2==0 ? now()->toDateString() : null,
                    'created_by'=>1,
                ]);
            } catch (\Exception $e) {}
            // weekly
            $periodeW = now()->format('Y-\WW');
            try {
                \App\Models\KasWarga::create([
                    'rt_id'=>$w->rt_id,
                    'warga_id'=>$w->id,
                    'periode_type'=>'weekly',
                    'periode'=>$periodeW,
                    'nominal'=>10000,
                    'status'=>'belum_bayar',
                    'created_by'=>1,
                ]);
            } catch (\Exception $e) {}
        }
    }
}
