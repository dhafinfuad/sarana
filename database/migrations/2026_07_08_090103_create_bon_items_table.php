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
        Schema::create('bon_items', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 30)->unique();
            $table->string('nama_barang', 100);
            $table->foreignId('satuan_id')->constrained('bon_satuans');
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(5);
            $table->string('image_path')->nullable(); // Foto barang, disimpan di storage/public/bon-items/
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_items');
    }
};
