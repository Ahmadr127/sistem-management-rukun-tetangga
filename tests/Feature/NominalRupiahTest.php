<?php

namespace Tests\Feature;

use App\Models\KasJenis;
use App\Models\Keuangan;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Rt;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Menjamin nominal rupiah yang diinput tidak keliru:
 * - Komponen x-rupiah-input mengirim angka murni (tanpa titik ribuan).
 * - Server menolak format bertitik / non-angka agar tidak tersimpan ngawur.
 */
class NominalRupiahTest extends TestCase
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

    private function makeUserWithPermissions(array $permissionNames, ?Rt $rt = null): User
    {
        $rt ??= Rt::create(['kode_rt' => '001', 'nama_rt' => 'RT 001', 'is_active' => true]);

        $role = Role::create(['name' => 'bendahara_' . uniqid(), 'display_name' => 'Bendahara', 'is_active' => true]);
        foreach ($permissionNames as $name) {
            $perm = Permission::firstOrCreate(
                ['name' => $name],
                ['display_name' => $name, 'description' => $name]
            );
            $role->permissions()->syncWithoutDetaching([$perm->id]);
        }

        return User::factory()->create([
            'username' => 'bendahara_' . uniqid(),
            'role_id' => $role->id,
            'rt_id' => $rt->id,
        ]);
    }

    // ---------- Keuangan (jumlah) ----------

    public function test_keuangan_store_menyimpan_jumlah_angka_murni_dengan_tepat(): void
    {
        $user = $this->makeUserWithPermissions(['manage_keuangan']);

        $res = $this->actingAs($user)->post(route('keuangan.store'), [
            'tanggal' => '2026-10-01',
            'jenis' => 'PEMASUKAN',
            'kategori' => 'Iuran Warga',
            'jumlah' => '20000000', // seperti yang dikirim hidden input x-rupiah-input
        ]);

        $res->assertRedirect(route('keuangan.pemasukan.index'));
        $this->assertDatabaseHas('keuangan', [
            'kategori' => 'Iuran Warga',
            'jumlah' => 20000000,
        ]);
        // Pastikan bukan keliru jadi 20 (mis. parsed "20.000.000" secara naif)
        $this->assertDatabaseMissing('keuangan', ['kategori' => 'Iuran Warga', 'jumlah' => 20]);
    }

    public function test_keuangan_store_menolak_jumlah_format_ribuan_bertitik(): void
    {
        $user = $this->makeUserWithPermissions(['manage_keuangan']);

        $res = $this->actingAs($user)->post(route('keuangan.store'), [
            'tanggal' => '2026-10-01',
            'jenis' => 'PEMASUKAN',
            'kategori' => 'Iuran Warga',
            'jumlah' => '20.000.000', // format tampilan — tidak boleh lolos
        ]);

        $res->assertSessionHasErrors('jumlah');
        $this->assertDatabaseMissing('keuangan', ['kategori' => 'Iuran Warga']);
    }

    public function test_keuangan_store_menolak_jumlah_negatif_huruf_dan_kosong(): void
    {
        $user = $this->makeUserWithPermissions(['manage_keuangan']);

        foreach (['-5000', 'dua puluh ribu', ''] as $i => $bad) {
            $res = $this->actingAs($user)->post(route('keuangan.store'), [
                'tanggal' => '2026-10-01',
                'jenis' => 'PENGELUARAN',
                'kategori' => "Konsumsi-{$i}",
                'jumlah' => $bad,
            ]);

            $res->assertSessionHasErrors('jumlah');
            $this->assertDatabaseMissing('keuangan', ['kategori' => "Konsumsi-{$i}"]);
        }
    }

    public function test_keuangan_update_menyimpan_jumlah_dengan_tepat(): void
    {
        $user = $this->makeUserWithPermissions(['manage_keuangan']);
        $trx = Keuangan::create([
            'tanggal' => '2026-10-01',
            'jenis' => 'PENGELUARAN',
            'kategori' => 'Konsumsi',
            'jumlah' => 50000,
            'rt_id' => $user->rt_id,
            'created_by' => $user->id,
        ]);

        $res = $this->actingAs($user)->put(route('keuangan.update', $trx), [
            'tanggal' => '2026-10-02',
            'jenis' => 'PENGELUARAN',
            'kategori' => 'Konsumsi',
            'jumlah' => '1500000',
        ]);

        $res->assertRedirect(route('keuangan.pengeluaran.index'));
        $this->assertDatabaseHas('keuangan', ['id' => $trx->id, 'jumlah' => 1500000]);
    }

    public function test_keuangan_update_menolak_jumlah_bertitik_dan_desimal(): void
    {
        $user = $this->makeUserWithPermissions(['manage_keuangan']);
        $trx = Keuangan::create([
            'tanggal' => '2026-10-01',
            'jenis' => 'PEMASUKAN',
            'kategori' => 'Donasi',
            'jumlah' => 100000,
            'rt_id' => $user->rt_id,
            'created_by' => $user->id,
        ]);

        foreach (['150.000', '150000.50'] as $bad) {
            $res = $this->actingAs($user)->put(route('keuangan.update', $trx), [
                'tanggal' => '2026-10-02',
                'jenis' => 'PEMASUKAN',
                'kategori' => 'Donasi',
                'jumlah' => $bad,
            ]);

            $res->assertSessionHasErrors('jumlah');
        }

        // Nilai asal tidak berubah
        $this->assertDatabaseHas('keuangan', ['id' => $trx->id, 'jumlah' => 100000]);
    }

    // ---------- Kas Warga jenis (nominal) ----------

    public function test_kas_jenis_store_menyimpan_nominal_dengan_tepat(): void
    {
        $user = $this->makeUserWithPermissions(['manage_kas']);

        $res = $this->actingAs($user)->post(route('kas-warga.store'), [
            'rt_id' => $user->rt_id,
            'nama' => 'Kas Bulanan',
            'periode_type' => 'monthly',
            'target_type' => 'kk',
            'nominal' => '25000',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('kas_jenis', ['nama' => 'Kas Bulanan', 'nominal' => 25000]);
    }

    public function test_kas_jenis_store_menolak_nominal_format_bertitik(): void
    {
        $user = $this->makeUserWithPermissions(['manage_kas']);

        $res = $this->actingAs($user)->post(route('kas-warga.store'), [
            'rt_id' => $user->rt_id,
            'nama' => 'Kas Mingguan',
            'periode_type' => 'weekly',
            'target_type' => 'perorangan',
            'nominal' => '25.000',
        ]);

        $res->assertSessionHasErrors('nominal');
        $this->assertDatabaseMissing('kas_jenis', ['nama' => 'Kas Mingguan']);
    }

    // ---------- Pembayaran kas (nominal_bayar) ----------

    public function test_bayar_menyimpan_nominal_bayar_dengan_tepat(): void
    {
        $user = $this->makeUserWithPermissions(['manage_kas']);
        $jenis = KasJenis::create([
            'rt_id' => $user->rt_id,
            'nama' => 'Kas Bulanan',
            'periode_type' => 'monthly',
            'nominal' => 20000,
            'target_type' => 'perorangan',
            'is_active' => true,
        ]);
        $warga = Warga::create([
            'rt_id' => $user->rt_id,
            'nik' => '1234567890123456',
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'WNI',
            'status_warga' => 'AKTIF',
        ]);

        $res = $this->actingAs($user)->postJson(route('kas-warga.bayar', $jenis), [
            'tanggal' => '2026-10-03',
            'nominal_bayar' => '20000',
            'warga_id' => $warga->id,
            'mode' => 'bulan',
            'bulan' => '2026-10',
        ]);

        $res->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('kas_pembayaran', [
            'kas_jenis_id' => $jenis->id,
            'warga_id' => $warga->id,
            'nominal_bayar' => 20000,
        ]);
    }

    public function test_bayar_menolak_nominal_format_bertitik(): void
    {
        $user = $this->makeUserWithPermissions(['manage_kas']);
        $jenis = KasJenis::create([
            'rt_id' => $user->rt_id,
            'nama' => 'Kas Bulanan',
            'periode_type' => 'monthly',
            'nominal' => 20000,
            'target_type' => 'perorangan',
            'is_active' => true,
        ]);
        $warga = Warga::create([
            'rt_id' => $user->rt_id,
            'nik' => '1234567890123456',
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'WNI',
            'status_warga' => 'AKTIF',
        ]);

        $res = $this->actingAs($user)->postJson(route('kas-warga.bayar', $jenis), [
            'tanggal' => '2026-10-03',
            'nominal_bayar' => '20.000',
            'warga_id' => $warga->id,
            'mode' => 'bulan',
            'bulan' => '2026-10',
        ]);

        $res->assertStatus(422)->assertJsonValidationErrors('nominal_bayar');
        $this->assertDatabaseMissing('kas_pembayaran', ['kas_jenis_id' => $jenis->id]);
    }

    // ---------- Form keuangan tanpa deskripsi ----------

    public function test_form_pemasukan_tanpa_input_deskripsi_tetap_ada_keterangan(): void
    {
        $user = $this->makeUserWithPermissions(['view_keuangan', 'manage_keuangan']);

        $res = $this->actingAs($user)->get(route('keuangan.pemasukan.create'));
        $res->assertOk();
        $res->assertSee('name="keterangan"', false);
        $res->assertDontSee('name="deskripsi"', false);

        $res = $this->actingAs($user)->get(route('keuangan.pemasukan.index'));
        $res->assertOk();
        $res->assertSee('name="keterangan"', false);
        $res->assertDontSee('name="deskripsi"', false);
    }

    public function test_form_pengeluaran_tanpa_input_deskripsi_tetap_ada_keterangan(): void
    {
        $user = $this->makeUserWithPermissions(['view_keuangan', 'manage_keuangan']);

        $res = $this->actingAs($user)->get(route('keuangan.pengeluaran.create'));
        $res->assertOk();
        $res->assertSee('name="keterangan"', false);
        $res->assertDontSee('name="deskripsi"', false);

        $res = $this->actingAs($user)->get(route('keuangan.pengeluaran.index'));
        $res->assertOk();
        $res->assertSee('name="keterangan"', false);
        $res->assertDontSee('name="deskripsi"', false);
    }
}
