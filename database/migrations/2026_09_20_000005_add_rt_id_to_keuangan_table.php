<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keuangan', function (Blueprint $table) {
            $table->foreignId('rt_id')->nullable()->after('created_by')->constrained('rts')->nullOnDelete()->cascadeOnUpdate();
            $table->index('rt_id');
        });
    }

    public function down(): void
    {
        Schema::table('keuangan', function (Blueprint $table) {
            $table->dropForeign(['rt_id']);
            $table->dropColumn('rt_id');
        });
    }
};
