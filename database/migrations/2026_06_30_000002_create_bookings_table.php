<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel bookings menyimpan histori dan transaksi pengajuan peminjaman kendaraan.
     * Menggunakan state management status yang jelas dengan enum 5 state.
     * Ketersediaan berbasis Tanggal penuh (bukan jam).
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Relasi ke pegawai peminjam
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete()
                  ->comment('Pegawai yang mengajukan peminjaman');

            // Relasi ke kendaraan
            $table->foreignId('vehicle_id')
                  ->constrained('vehicles')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete()
                  ->comment('Kendaraan yang dipinjam');

            // Detail pengajuan (wajib diisi — mandatory per PRD)
            $table->string('provinsi')->comment('Provinsi tujuan');
            $table->string('kota')->comment('Kota / Kabupaten tujuan');
            $table->text('keperluan')->comment('Uraian keperluan / maksud perjalanan dinas');

            // Rentang waktu berbasis hari penuh (bukan jam)
            $table->date('tanggal_mulai')->comment('Tanggal awal peminjaman (inklusif)');
            $table->date('tanggal_selesai')->comment('Tanggal akhir peminjaman (inklusif)');

            // State Management Status (5 state)
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'selesai', 'dibatalkan'])
                  ->default('pending')
                  ->comment('pending=menunggu; disetujui=admin OK; ditolak=admin tolak; selesai=kendaraan dikembalikan; dibatalkan=admin batalkan darurat');

            // Catatan opsional dari admin (alasan tolak / batalkan)
            $table->text('catatan_admin')->nullable()->comment('Catatan / alasan dari administrator (opsional)');

            // Audit: siapa yang melakukan approval/penolakan/pembatalan
            $table->foreignId('processed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('ID admin yang memproses persetujuan / penolakan / pembatalan');

            $table->timestamp('processed_at')->nullable()->comment('Waktu pemrosesan oleh admin');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
