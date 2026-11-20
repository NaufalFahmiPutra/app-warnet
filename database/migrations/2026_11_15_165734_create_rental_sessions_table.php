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
        Schema::create('rental_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('computer_id')->constrained('computers');
            $table->foreignId('member_id')->nullable()->constrained('members');
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai')->nullable();
            $table->integer('durasi_menit')->nullable();
            $table->integer('total_biaya')->nullable();
            $table->enum('status', ['berjalan', 'selesai'])->default('berjalan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_sessions');
    }
};
