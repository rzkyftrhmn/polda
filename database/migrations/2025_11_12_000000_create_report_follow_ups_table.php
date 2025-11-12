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
        Schema::create('report_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_follow_ups');
    }
};
