<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            // Tambah kolom jika belum ada
            if (!Schema::hasColumn('programs', 'student_count')) {
                $table->integer('student_count')->default(0)->after('description');
            }
            if (!Schema::hasColumn('programs', 'teacher_count')) {
                $table->integer('teacher_count')->default(0)->after('student_count');
            }
            if (!Schema::hasColumn('programs', 'facilities')) {
                $table->text('facilities')->nullable()->after('teacher_count');
            }
            if (!Schema::hasColumn('programs', 'products')) {
                $table->text('products')->nullable()->after('facilities');
            }
            if (!Schema::hasColumn('programs', 'achievements')) {
                $table->text('achievements')->nullable()->after('products');
            }
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['student_count', 'teacher_count', 'facilities', 'products', 'achievements']);
        });
    }
};