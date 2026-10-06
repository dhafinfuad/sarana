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
        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id('id_surat_keluar');
            $table->integer('vrb_id')->nullable();
            $table->string('jenis_surat', 10);
            $table->string('jenis_pj', 30);
            $table->integer('nomor_surat');
            $table->integer('tahun_surat');
            $table->date('tgl_surat');
            $table->string('perihal', 400)->nullable();
            $table->string('tujuan_surat', 400)->nullable();
            $table->string('perekam', 9);
            $table->date('tgl_rekam');
            $table->string('keterangan', 400)->nullable();
            $table->integer('link_id')->nullable();
            $table->date('tgl_kirim')->nullable();
            $table->string('pengirim', 9)->nullable();
            $table->string('batal', 1)->nullable();
            $table->string('keterangan_pengiriman')->nullable();
            
            $table->index('vrb_id');
            $table->index('jenis_surat');
            $table->index('jenis_pj');
            $table->index('nomor_surat');
            $table->index('tahun_surat');
            $table->index('tgl_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluar');
    }
};
