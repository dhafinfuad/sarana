<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_nomor_awal', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_surat', 10);
            $table->string('jenis_pj', 30);
            $table->string('tahun', 4);
            $table->integer('nomor_awal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_nomor_awal');
    }
};
