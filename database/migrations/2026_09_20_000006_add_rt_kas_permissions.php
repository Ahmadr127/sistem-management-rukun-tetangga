<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['name' => 'manage_rt', 'display_name' => 'Kelola RT', 'description' => 'Mengelola data RT'],
            ['name' => 'view_rt', 'display_name' => 'Lihat RT', 'description' => 'Melihat data RT'],
            ['name' => 'manage_kas', 'display_name' => 'Kelola Kas Warga', 'description' => 'Mengelola kas warga mingguan/bulanan'],
            ['name' => 'view_kas', 'display_name' => 'Lihat Kas Warga', 'description' => 'Melihat kas warga'],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $p['name']],
                array_merge($p, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $adminRole = DB::table('roles')->whereIn('name', ['admin','superadmin'])->first();
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

    public function down(): void {}
};
