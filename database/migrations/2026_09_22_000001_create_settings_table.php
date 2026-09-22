<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->string('display_name', 150);
            $table->enum('type', ['text', 'textarea', 'image'])->default('text');
            $table->text('value')->nullable();
            $table->string('description', 255)->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // Default pengaturan sistem (logo & nama)
        $defaults = [
            [
                'key' => 'site_name',
                'display_name' => 'Nama Sistem',
                'type' => 'text',
                'value' => 'Sistem Manajemen Rukun Tetangga',
                'description' => 'Nama lengkap sistem, tampil di judul halaman & header.',
                'is_system' => true,
            ],
            [
                'key' => 'site_short_name',
                'display_name' => 'Nama Singkat Sistem',
                'type' => 'text',
                'value' => 'SI-RT',
                'description' => 'Nama singkat untuk logo/brand di sidebar & navigasi.',
                'is_system' => true,
            ],
            [
                'key' => 'site_logo',
                'display_name' => 'Logo Sistem',
                'type' => 'image',
                'value' => null,
                'description' => 'Logo sistem (PNG/JPG/SVG, maks 2MB). Kosongkan untuk memakai logo bawaan.',
                'is_system' => true,
            ],
        ];

        foreach ($defaults as $row) {
            DB::table('settings')->updateOrInsert(
                ['key' => $row['key']],
                array_merge($row, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Permission CRUD pengaturan
        $permissions = [
            ['name' => 'view_settings', 'display_name' => 'Lihat Pengaturan', 'description' => 'Melihat pengaturan sistem (logo & nama)'],
            ['name' => 'manage_settings', 'display_name' => 'Kelola Pengaturan', 'description' => 'Mengubah logo, nama sistem & pengaturan lain'],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $p['name']],
                array_merge($p, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $adminRole = DB::table('roles')->whereIn('name', ['admin', 'superadmin'])->first();
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
        Schema::dropIfExists('settings');
        DB::table('permissions')->whereIn('name', ['view_settings', 'manage_settings'])->delete();
    }
};
