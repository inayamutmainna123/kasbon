<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update guard_name from 'karyawan' to 'web' in roles table
        DB::table('roles')->where('guard_name', 'karyawan')->update(['guard_name' => 'web']);

        // Update guard_name from 'karyawan' to 'web' in permissions table
        DB::table('permissions')->where('guard_name', 'karyawan')->update(['guard_name' => 'web']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally reverse the changes if needed
        DB::table('roles')->where('guard_name', 'web')->update(['guard_name' => 'karyawan']);
        DB::table('permissions')->where('guard_name', 'web')->update(['guard_name' => 'karyawan']);
    }
};
