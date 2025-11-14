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

    public function getTrendReports($days = 14)
    {
        $startDate = now()->subDays($days - 1)->startOfDay();

        return Report::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    public function getTotalReports()
    {
        return Report::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    }

    public function getTopCategoryAktif()
    {
        return \DB::table('reports')
            ->join('report_categories', 'report_categories.id', '=', 'reports.category_id')
            ->select('report_categories.name as category', \DB::raw('COUNT(reports.id) as total'))
            ->whereIn('reports.status', ['PEMERIKSAAN', 'LIMPAH', 'SIDANG'])
            ->groupBy('reports.category_id', 'report_categories.name')
            ->orderByDesc('total')
            ->first(); 
    }

    public function getLaporanAktif()
    {
        return Report::whereIn('status', ['PEMERIKSAAN', 'LIMPAH', 'SIDANG'])
            ->count();
    }

    public function getPersentasiLaporanSelesai()
    {
        $total = Report::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $selesai = Report::where('status', 'SELESAI')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if ($total == 0) {
            return 0;
        }

        return round(($selesai / $total) * 100);
    }
}
