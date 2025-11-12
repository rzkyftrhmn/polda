<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('report_evidence') && ! Schema::hasTable('report_evidences')) {
            Schema::rename('report_evidence', 'report_evidences');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('report_evidences') && ! Schema::hasTable('report_evidence')) {
            Schema::rename('report_evidences', 'report_evidence');
        }
    }
};
