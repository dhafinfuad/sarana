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
        Schema::create('bon_requests', function (Blueprint $table) {
            $table->id();
            $table->string('no_bon', 25)->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->text('keperluan');
            $table->enum('status', ['menunggu', 'diproses', 'ditolak', 'dibatalkan'])->default('menunggu');
            $table->text('catatan_petugas')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_requests');
    }
};
