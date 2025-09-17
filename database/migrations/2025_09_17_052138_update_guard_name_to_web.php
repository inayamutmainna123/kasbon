<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('roles')->where('guard_name', 'karyawan')->update(['guard_name' => 'web']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->where('guard_name', 'web')->update(['guard_name' => 'karyawan']);
    }
};
