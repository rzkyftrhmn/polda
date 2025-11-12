<?php

namespace App\Repositories;

use App\Models\Report;

class DashboardRepository
{
    public function getStatusSummary()
    {
        $baru = Report::where('status', 'SUBMITTED')->count();
        $diproses = Report::whereIn('status', ['PEMERIKSAAN', 'LIMPAH', 'SIDANG'])->count();
        $selesai = Report::where('status', 'SELESAI')->count();

        return compact('baru', 'diproses', 'selesai');
    }
}
