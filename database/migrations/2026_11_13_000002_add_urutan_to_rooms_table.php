<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->unsignedInteger('urutan')->default(0)->after('status');
        });

        // Urutan awal mengikuti urutan nama yang selama ini dipakai.
        DB::table('rooms')->orderBy('nama')->pluck('id')->each(
            fn ($id, $i) => DB::table('rooms')->where('id', $id)->update(['urutan' => $i + 1])
        );
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('urutan');
        });
    }
};
