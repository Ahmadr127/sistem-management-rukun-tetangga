<?php

namespace Tests\Feature;

use App\Models\KasJenis;
use App\Models\KasPembayaran;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Rt;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KasWargaAjaxTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Migrasi proyek memakai fungsi MySQL CONCAT yang tidak ada di SQLite.
     * Daftarkan padanannya sebelum migrasi test dijalankan.
     */
    protected function beforeRefreshingDatabase(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::connection()->getPdo()->sqliteCreateFunction(
                'CONCAT',
                fn (...$args) => implode('', array_map(fn ($v) => (string) $v, $args)),
                -1
            );
        }
    }

    private function setupKas(): array
    {
        $rt = Rt::create(['kode_rt' => '001', 'nama_rt' => 'RT 001', 'is_active' => true]);

        $role = Role::create(['name' => 'operator', 'display_name' => 'Operator', 'is_active' => true]);
        // Permission manage_kas sudah di-seed oleh migrasi add_rt_kas_permissions
        $perm = Permission::firstOrCreate(
            ['name' => 'manage_kas'],
            ['display_name' => 'Kelola Kas Warga', 'description' => 'Mengelola kas warga']
        );
        $role->permissions()->syncWithoutDetaching([$perm->id]);

        $user = User::factory()->create([
            'username' => 'operator_kas',
            'role_id' => $role->id,
            'rt_id' => $rt->id,
        ]);

        $jenis = KasJenis::create([
            'rt_id' => $rt->id,
            'nama' => 'Kas Bulanan',
            'periode_type' => 'monthly',
            'nominal' => 10000,
            'target_type' => 'perorangan',
            'is_active' => true,
        ]);

        $warga = Warga::create([
            'rt_id' => $rt->id,
            'nik' => '1234567890123456',
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'WNI',
            'status_warga' => 'AKTIF',
        ]);

        return compact('rt', 'role', 'user', 'jenis', 'warga');
    }

    public function test_bayar_json_menyimpan_dan_mengembalikan_stats(): void
    {
        ['user' => $user, 'jenis' => $jenis, 'warga' => $warga] = $this->setupKas();

        $res = $this->actingAs($user)->postJson(route('kas-warga.bayar', $jenis), [
            'tanggal' => '2026-09-05',
            'nominal_bayar' => 10000,
            'warga_id' => $warga->id,
            'mode' => 'bulan',
            'bulan' => '2026-09',
        ]);

        $res->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'pembayaran' => ['id', 'nominal_bayar', 'nominal_format', 'waktu', 'waktu_full', 'tanggal'],
                'stats' => ['terbayar', 'belum', 'rupiah', 'persen'],
            ])
            ->assertJsonPath('stats.terbayar', 1);

        $this->assertDatabaseHas('kas_pembayaran', [
            'kas_jenis_id' => $jenis->id,
            'warga_id' => $warga->id,
            'tanggal' => '2026-09-05 00:00:00',
            'status' => 'sudah_bayar',
        ]);
    }

    public function test_bayar_json_validasi_gagal_422(): void
    {
        ['user' => $user, 'jenis' => $jenis, 'warga' => $warga] = $this->setupKas();

        $res = $this->actingAs($user)->postJson(route('kas-warga.bayar', $jenis), [
            'tanggal' => '2026-09-05',
            'warga_id' => $warga->id,
        ]);

        $res->assertStatus(422)->assertJsonValidationErrors('nominal_bayar');
    }

    public function test_batal_bayar_json_menghapus_dan_mengembalikan_stats(): void
    {
        ['user' => $user, 'jenis' => $jenis, 'warga' => $warga] = $this->setupKas();

        $bayar = KasPembayaran::create([
            'kas_jenis_id' => $jenis->id,
            'warga_id' => $warga->id,
            'kartu_keluarga_id' => null,
            'tanggal' => '2026-09-05',
            'nominal_bayar' => 10000,
            'waktu_bayar' => now(),
            'status' => 'sudah_bayar',
        ]);

        $res = $this->actingAs($user)->deleteJson(
            route('kas-warga.batal-bayar', $bayar) . '?mode=bulan&bulan=2026-09'
        );

        $res->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('stats.terbayar', 0);

        $this->assertDatabaseMissing('kas_pembayaran', ['id' => $bayar->id]);
    }
}
