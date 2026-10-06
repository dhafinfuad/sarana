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
        Schema::create('bon_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bon_request_id')->constrained('bon_requests')->cascadeOnDelete();
            $table->foreignId('bon_item_id')->constrained('bon_items');
            $table->string('nama_barang', 100);  // snapshot
            $table->string('satuan', 50);        // snapshot
            $table->integer('jumlah_diminta');
            $table->integer('jumlah_diberikan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_request_items');
    }
};
