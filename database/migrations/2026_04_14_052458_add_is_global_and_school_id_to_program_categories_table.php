<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('program_categories', function (Blueprint $table) {
            $table->boolean('is_global')->default(true)->after('description');
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade')->after('is_global');
            $table->foreignId('created_by')->nullable()->constrained('users')->after('school_id');
        });
    }

    public function down()
    {
        Schema::table('program_categories', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['is_global', 'school_id', 'created_by']);
        });
    }
};