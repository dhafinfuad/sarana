<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel vehicles menyimpan master data armada kendaraan dinas:
     * plat nomor, nama kendaraan, dan status ketersediaan berbasis enum.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plat_nomor', 20)->unique()->comment('Nomor plat kendaraan, unik');
            $table->string('nama_kendaraan')->comment('Nama / tipe kendaraan, contoh: Toyota Kijang Innova');
            $table->enum('status', ['tersedia', 'tidak_tersedia'])->default('tersedia')
                  ->comment('tersedia = siap dipinjam; tidak_tersedia = sedang servis / rusak');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
