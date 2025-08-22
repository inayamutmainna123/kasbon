<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('karyawan_id')->nullable();
            $table->foreignUlid('user_id')->nullable();
            $table->char('kasbon_id', 26); // ULID kasbon
            $table->decimal('jumlah_bayar', 15, 2);
            $table->enum('metode', ['potong_gaji', 'manual']);
            $table->date('tanggal_bayar')->default(now());
            $table->timestamps();

            // Foreign key manual
            $table->foreign('kasbon_id')
                ->references('id')
                ->on('kasbon') // tabel singular
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
