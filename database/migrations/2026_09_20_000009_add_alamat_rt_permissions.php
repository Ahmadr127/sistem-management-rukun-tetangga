<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $perms = [
            ['name' => 'manage_alamat_rt', 'display_name' => 'Kelola Alamat RT', 'description' => 'Mengelola master alamat RT'],
            ['name' => 'view_alamat_rt', 'display_name' => 'Lihat Alamat RT', 'description' => 'Melihat master alamat RT'],
        ];
        foreach ($perms as $p) {
            DB::table('permissions')->updateOrInsert(['name'=>$p['name']], array_merge($p, ['created_at'=>now(),'updated_at'=>now()]));
        }
        $admin = DB::table('roles')->where('name','admin')->first();
        if ($admin) {
            foreach ($perms as $p) {
                $perm = DB::table('permissions')->where('name',$p['name'])->first();
                if ($perm) DB::table('role_permission')->updateOrInsert(['role_id'=>$admin->id,'permission_id'=>$perm->id],['role_id'=>$admin->id,'permission_id'=>$perm->id]);
            }
        }
        $rtRole = DB::table('roles')->where('name','rt')->first();
        if ($rtRole) {
            $perm = DB::table('permissions')->where('name','view_alamat_rt')->first();
            if ($perm) DB::table('role_permission')->updateOrInsert(['role_id'=>$rtRole->id,'permission_id'=>$perm->id],['role_id'=>$rtRole->id,'permission_id'=>$perm->id]);
        }
    }
    public function down(): void {}
};
