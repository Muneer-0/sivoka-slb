<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 10)->unique();
            $table->string('name');
            $table->text('address');
            $table->string('village')->nullable();
            $table->string('district');
            $table->string('city');
            $table->string('province')->default('Sumatera Utara');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('headmaster')->nullable();
            $table->enum('status', ['negeri', 'swasta'])->default('swasta');
            $table->string('accreditation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};