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

        $kkData = [
            ['no_kk'=>'3270010101010001','kepala'=>'Ahmad','rt'=>'01','rt_id'=>$rt1?->id,'rw'=>'01','alamat'=>'Jl. Mawar 1'],
            ['no_kk'=>'3270010101010002','kepala'=>'Budi','rt'=>'01','rt_id'=>$rt1?->id,'rw'=>'01','alamat'=>'Jl. Mawar 2'],
            ['no_kk'=>'3270010101010003','kepala'=>'Citra','rt'=>'02','rt_id'=>$rt2?->id,'rw'=>'01','alamat'=>'Jl. Melati 1'],
            ['no_kk'=>'3270010101010004','kepala'=>'Deni','rt'=>'02','rt_id'=>$rt2?->id,'rw'=>'01','alamat'=>'Jl. Melati 2'],
        ];
        $wargaNames = [
            ['nama'=>'Ahmad','nik'=>'3270010101010001','rt_id'=>$rt1?->id],
            ['nama'=>'Budi','nik'=>'3270010101010002','rt_id'=>$rt1?->id],
            ['nama'=>'Siti','nik'=>'3270010101010003','rt_id'=>$rt1?->id],
            ['nama'=>'Citra','nik'=>'3270010101010004','rt_id'=>$rt2?->id],
            ['nama'=>'Deni','nik'=>'3270010101010005','rt_id'=>$rt2?->id],
            ['nama'=>'Eka','nik'=>'3270010101010006','rt_id'=>$rt2?->id],
        ];
        $kks = [];
        foreach ($kkData as $d) {
            $kks[] = \App\Models\KartuKeluarga::create([
                'no_kk'=>$d['no_kk'],
                'kepala_keluarga'=>$d['kepala'],
                'alamat'=>$d['alamat'],
                'rt'=>$d['rt'],
                'rw'=>$d['rw'],
                'rt_id'=>$d['rt_id'],
                'desa'=>'Demo','kecamatan'=>'Demo','kabupaten'=>'Demo','provinsi'=>'Jabar',
            ]);
        }
        foreach ($wargaNames as $idx => $w) {
            $kk = $kks[$idx % count($kks)];
            // ensure rt consistency: warga rt_id matches kk rt_id
            \App\Models\Warga::create([
                'kartu_keluarga_id'=>$kk->id,
                'rt_id'=>$w['rt_id'] ?? $kk->rt_id,
                'nik'=>$w['nik'],
                'nama'=>$w['nama'],
                'jenis_kelamin'=> $idx %2==0 ? 'L':'P',
                'status_warga'=>'AKTIF',
                'tempat_lahir'=>'Bandung',
                'tanggal_lahir'=> now()->subYears(20+$idx)->toDateString(),
            ]);
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
