<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('npsn', 8)->unique()->nullable()->after('email');
            // Ubah email menjadi nullable (karena sekarang pakai NPSN)
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('npsn');
            $table->string('email')->nullable(false)->change();
        });
    }
};