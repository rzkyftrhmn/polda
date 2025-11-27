<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Ensure column exists before attempting to alter
            if (Schema::hasColumn('reports', 'finish_time')) {
                DB::statement('ALTER TABLE reports MODIFY finish_time DATETIME NULL');
            } else {
                $table->dateTime('finish_time')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'finish_time')) {
                DB::statement('ALTER TABLE reports MODIFY finish_time INTEGER NULL');
            }
        });
    }
};
