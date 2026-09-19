<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            ['name' => 'manage_roles', 'display_name' => 'Kelola Roles', 'description' => 'Mengelola roles dan permissions'],
            ['name' => 'manage_permissions', 'display_name' => 'Kelola Permissions', 'description' => 'Mengelola permissions'],
            ['name' => 'view_dashboard', 'display_name' => 'Lihat Dashboard', 'description' => 'Melihat halaman dashboard'],
            ['name' => 'manage_users', 'display_name' => 'Kelola Users', 'description' => 'Mengelola pengguna'],
            ['name' => 'manage_organization_types', 'display_name' => 'Kelola Tipe Organisasi', 'description' => 'Mengelola tipe organisasi'],
            ['name' => 'manage_organization_units', 'display_name' => 'Kelola Unit Organisasi', 'description' => 'Mengelola unit organisasi'],
            // Warga & KK
            ['name' => 'manage_warga', 'display_name' => 'Kelola Warga', 'description' => 'Mengelola data warga'],
            ['name' => 'view_warga', 'display_name' => 'Lihat Warga', 'description' => 'Melihat data warga'],
            ['name' => 'manage_kk', 'display_name' => 'Kelola Kartu Keluarga', 'description' => 'Mengelola data KK'],
            ['name' => 'view_kk', 'display_name' => 'Lihat Kartu Keluarga', 'description' => 'Melihat data KK'],
            ['name' => 'manage_mutasi', 'display_name' => 'Kelola Mutasi Warga', 'description' => 'Mengelola mutasi warga'],
            ['name' => 'view_mutasi', 'display_name' => 'Lihat Mutasi', 'description' => 'Melihat mutasi warga'],
            // Keuangan
            ['name' => 'manage_keuangan', 'display_name' => 'Kelola Keuangan', 'description' => 'Mengelola transaksi keuangan'],
            ['name' => 'view_keuangan', 'display_name' => 'Lihat Keuangan', 'description' => 'Melihat laporan keuangan'],
            ['name' => 'view_laporan_keuangan', 'display_name' => 'Lihat Laporan Keuangan', 'description' => 'Melihat rekap laporan keuangan'],
            // Inventaris
            ['name' => 'manage_inventaris', 'display_name' => 'Kelola Inventaris', 'description' => 'Mengelola data inventaris'],
            ['name' => 'view_inventaris', 'display_name' => 'Lihat Inventaris', 'description' => 'Melihat data inventaris'],
            ['name' => 'manage_peminjaman', 'display_name' => 'Kelola Peminjaman', 'description' => 'Mengelola peminjaman inventaris'],
            ['name' => 'view_peminjaman', 'display_name' => 'Lihat Peminjaman', 'description' => 'Melihat peminjaman inventaris'],
            ['name' => 'view_laporan_inventaris', 'display_name' => 'Lihat Laporan Inventaris', 'description' => 'Melihat rekap inventaris'],
            ['name' => 'view_activity_logs', 'display_name' => 'Lihat Audit Log', 'description' => 'Melihat riwayat aktivitas'],
            // RT & Kas Warga
            ['name' => 'manage_rt', 'display_name' => 'Kelola RT', 'description' => 'Mengelola data RT'],
            ['name' => 'view_rt', 'display_name' => 'Lihat RT', 'description' => 'Melihat data RT'],
            ['name' => 'manage_kas', 'display_name' => 'Kelola Kas Warga', 'description' => 'Mengelola kas warga mingguan/bulanan'],
            ['name' => 'view_kas', 'display_name' => 'Lihat Kas Warga', 'description' => 'Melihat kas warga'],
            ['name' => 'manage_alamat_rt', 'display_name' => 'Kelola Alamat RT', 'description' => 'Mengelola master alamat RT'],
            ['name' => 'view_alamat_rt', 'display_name' => 'Lihat Alamat RT', 'description' => 'Melihat master alamat RT'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create Roles - idempotent for migrate:fresh where migrations already inserted permissions
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Superadmin', 'description' => 'Role dengan akses penuh ke sistem']
        );
        // Alias superadmin for clarity, keep admin for backward compat
        $superadminRole = Role::firstOrCreate(
            ['name' => 'superadmin'],
            ['display_name' => 'Superadmin', 'description' => 'Role superadmin - akses penuh']
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['display_name' => 'Pengguna', 'description' => 'Role untuk pengguna umum']
        );
        $rtRole = Role::firstOrCreate(
            ['name' => 'rt'],
            ['display_name' => 'Pengurus RT', 'description' => 'Role pengurus RT - akses terbatas sesuai RT']
        );

        // Assign permissions to roles - use syncWithoutDetaching to avoid duplicate pivot errors
        $allPerms = Permission::all()->pluck('id')->toArray();
        $adminRole->permissions()->syncWithoutDetaching($allPerms);
        $superadminRole->permissions()->syncWithoutDetaching($allPerms);
        
        $userRole->permissions()->syncWithoutDetaching(
            Permission::whereIn('name', [
                'view_dashboard'
            ])->pluck('id')->toArray()
        );

        // RT role gets scoped permissions (filtering enforced in backend)
        $rtPerms = Permission::whereIn('name', [
            'view_dashboard',
            'view_warga','manage_warga',
            'view_kk','manage_kk',
            'view_mutasi','manage_mutasi',
            'view_keuangan','manage_keuangan','view_laporan_keuangan',
            'view_inventaris','manage_inventaris','view_peminjaman','manage_peminjaman','view_laporan_inventaris',
            'view_rt',
            'view_kas','manage_kas',
            'view_alamat_rt',
            'view_activity_logs',
        ])->pluck('id')->toArray();
        $rtRole->permissions()->syncWithoutDetaching($rtPerms);
    }
}
