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
        Schema::create('kasbon', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('karyawan_id')->nullable();
            $table->bigInteger('jumlah');
            $table->text('alasan');
            $table->enum('status', ['pending', 'approved', 'rejected', 'lunas'])->default('pending');
            $table->date('tanggal_pengajuan')->default(now());
            $table->date('tanggal_approval')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasbon');
    }
};
