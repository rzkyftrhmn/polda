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

    public function getTopCategories($limit = 5)
    {
        return Report::select('report_categories.name as category', \DB::raw('count(reports.id) as total'))
            ->join('report_categories', 'reports.category_id', '=', 'report_categories.id')
            ->groupBy('report_categories.id', 'report_categories.name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

}
