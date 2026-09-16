<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['name' => 'manage_warga', 'display_name' => 'Kelola Warga', 'description' => 'Mengelola data warga'],
            ['name' => 'view_warga', 'display_name' => 'Lihat Warga', 'description' => 'Melihat data warga'],
            ['name' => 'manage_kk', 'display_name' => 'Kelola Kartu Keluarga', 'description' => 'Mengelola data KK'],
            ['name' => 'view_kk', 'display_name' => 'Lihat Kartu Keluarga', 'description' => 'Melihat data KK'],
            ['name' => 'manage_mutasi', 'display_name' => 'Kelola Mutasi Warga', 'description' => 'Mengelola mutasi warga'],
            ['name' => 'view_mutasi', 'display_name' => 'Lihat Mutasi', 'description' => 'Melihat mutasi warga'],
            ['name' => 'manage_keuangan', 'display_name' => 'Kelola Keuangan', 'description' => 'Mengelola transaksi keuangan'],
            ['name' => 'view_keuangan', 'display_name' => 'Lihat Keuangan', 'description' => 'Melihat laporan keuangan'],
            ['name' => 'view_laporan_keuangan', 'display_name' => 'Lihat Laporan Keuangan', 'description' => 'Melihat rekap laporan keuangan'],
            ['name' => 'manage_inventaris', 'display_name' => 'Kelola Inventaris', 'description' => 'Mengelola data inventaris'],
            ['name' => 'view_inventaris', 'display_name' => 'Lihat Inventaris', 'description' => 'Melihat data inventaris'],
            ['name' => 'manage_peminjaman', 'display_name' => 'Kelola Peminjaman', 'description' => 'Mengelola peminjaman inventaris'],
            ['name' => 'view_peminjaman', 'display_name' => 'Lihat Peminjaman', 'description' => 'Melihat peminjaman inventaris'],
            ['name' => 'view_laporan_inventaris', 'display_name' => 'Lihat Laporan Inventaris', 'description' => 'Melihat rekap inventaris'],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $p['name']],
                array_merge($p, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Assign all new permissions to admin role
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            foreach ($permissions as $p) {
                $perm = DB::table('permissions')->where('name', $p['name'])->first();
                if ($perm) {
                    DB::table('role_permission')->updateOrInsert(
                        ['role_id' => $adminRole->id, 'permission_id' => $perm->id],
                        ['role_id' => $adminRole->id, 'permission_id' => $perm->id]
                    );
                }
            }
        }
    }

    public function down(): void
    {
        // Keep permissions on rollback to avoid FK issues
    }
};
