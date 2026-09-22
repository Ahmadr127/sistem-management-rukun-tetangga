<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingTest extends TestCase
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

    private function makeUser(array $permissions = []): User
    {
        $role = Role::create(['name' => 'test-' . uniqid(), 'display_name' => 'Test', 'is_active' => true]);
        foreach ($permissions as $name) {
            $perm = Permission::firstOrCreate(
                ['name' => $name],
                ['display_name' => $name, 'description' => $name]
            );
            $role->permissions()->syncWithoutDetaching([$perm->id]);
        }

        return User::factory()->create([
            'username' => 'user-' . uniqid(),
            'role_id' => $role->id,
        ]);
    }

    public function test_index_hanya_untuk_yang_punya_akses(): void
    {
        $admin = $this->makeUser(['view_settings', 'manage_settings']);
        $this->actingAs($admin)->get(route('settings.index'))
            ->assertOk()
            ->assertSee('Sistem Manajemen Rukun Tetangga');

        $guest = $this->makeUser([]);
        $this->actingAs($guest)->get(route('settings.index'))->assertForbidden();
    }

    public function test_store_pengaturan_teks(): void
    {
        $admin = $this->makeUser(['view_settings', 'manage_settings']);

        $res = $this->actingAs($admin)->post(route('settings.store'), [
            'key' => 'footer_text',
            'display_name' => 'Teks Footer',
            'type' => 'text',
            'value' => 'RT 001 Makmur',
        ]);

        $res->assertRedirect(route('settings.index'));
        $this->assertDatabaseHas('settings', ['key' => 'footer_text', 'value' => 'RT 001 Makmur']);
    }

    public function test_store_logo_menyimpan_file(): void
    {
        Storage::fake('public');
        $admin = $this->makeUser(['view_settings', 'manage_settings']);

        $res = $this->actingAs($admin)->post(route('settings.store'), [
            'key' => 'banner',
            'display_name' => 'Banner',
            'type' => 'image',
            'logo' => UploadedFile::fake()->image('banner.png'),
        ]);

        $res->assertRedirect(route('settings.index'));
        $path = Setting::where('key', 'banner')->value('value');
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_update_nama_sistem(): void
    {
        $admin = $this->makeUser(['view_settings', 'manage_settings']);
        $siteName = Setting::where('key', 'site_name')->firstOrFail();

        $res = $this->actingAs($admin)->put(route('settings.update', $siteName), [
            'display_name' => 'Nama Sistem',
            'type' => 'text',
            'value' => 'Sistem RT 001 Baru',
        ]);

        $res->assertRedirect(route('settings.index'));
        $this->assertSame('Sistem RT 001 Baru', Setting::get('site_name'));
    }

    public function test_destroy_pengaturan_sistem_ditolak(): void
    {
        $admin = $this->makeUser(['view_settings', 'manage_settings']);
        $siteName = Setting::where('key', 'site_name')->firstOrFail();

        $res = $this->actingAs($admin)->delete(route('settings.destroy', $siteName));

        $res->assertRedirect(route('settings.index'));
        $this->assertDatabaseHas('settings', ['key' => 'site_name']);
    }

    public function test_destroy_pengaturan_biasa_menghapus_file(): void
    {
        Storage::fake('public');
        $admin = $this->makeUser(['view_settings', 'manage_settings']);

        $setting = Setting::create([
            'key' => 'sementara',
            'display_name' => 'Sementara',
            'type' => 'image',
            'value' => UploadedFile::fake()->image('x.png')->store('logos', 'public'),
        ]);
        Storage::disk('public')->assertExists($setting->value);

        $this->actingAs($admin)->delete(route('settings.destroy', $setting))
            ->assertRedirect(route('settings.index'));

        $this->assertDatabaseMissing('settings', ['key' => 'sementara']);
        Storage::disk('public')->assertMissing($setting->value);
    }

    public function test_tanpa_permission_manage_tidak_bisa_simpan(): void
    {
        $viewer = $this->makeUser(['view_settings']);

        $this->actingAs($viewer)->post(route('settings.store'), [
            'key' => 'coba',
            'display_name' => 'Coba',
            'type' => 'text',
            'value' => 'x',
        ])->assertForbidden();
    }
}
