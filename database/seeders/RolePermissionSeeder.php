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
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create Roles
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Role dengan akses penuh ke sistem'
        ]);


        $userRole = Role::create([
            'name' => 'user',
            'display_name' => 'Pengguna',
            'description' => 'Role untuk pengguna umum'
        ]);

        // Assign permissions to roles
        $adminRole->permissions()->attach(Permission::all()); // Admin gets all permissions
        
        
        $userRole->permissions()->attach(
            Permission::whereIn('name', [
                'view_dashboard'
            ])->get()
        );
    }
}
