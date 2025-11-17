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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->string('title');
            $table->datetime('incident_datetime');
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->text('address_detail')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->enum('status', ['SUBMITTED', 'PEMERIKSAAN', 'LIMPAH', 'SIDANG', 'SELESAI'])->default('SUBMITTED');
            $table->text('description')->nullable();
            $table->string('name_of_reporter');
            $table->text('address_of_reporter');
            $table->string('phone_of_reporter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
