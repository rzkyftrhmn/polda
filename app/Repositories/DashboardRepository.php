<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\Suspect;
use App\Models\Institution;
use App\Models\ReportCategory;
use App\Models\ReportJourney;
use Illuminate\Support\Facades\DB;

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

    public function getAverageResolutionTime()
    {
        try {
            $results = ReportJourney::where('report_journeys.type', 'SELESAI')
                ->join('reports', 'reports.id', '=', 'report_journeys.report_id')
                ->whereNotNull('reports.created_at')
                ->whereNotNull('report_journeys.created_at')
                ->select(DB::raw('TIMESTAMPDIFF(DAY, reports.created_at, report_journeys.created_at) as diff'))
                ->pluck('diff');

            if ($results->isEmpty()) {
                return 0;
            }

            return round($results->avg(), 1);
        } catch (\Exception $e) {
            \Log::error('AVG RESOLUTION ERROR: ' . $e->getMessage());
            return 0;
        }
    }

    public function getRecentReports()
    {
        return Report::with([
            'suspects',
            'journeys.institution',
            'category'
        ])
        ->orderBy('created_at', 'desc')
        ->take(12)
        ->get()
        ->map(function ($report) {
            $pelapor = $report->suspects->sortBy('id')->first()?->name ?? '-';
            $institusi = $report->journeys
                ->filter(fn($j) => $j->institution)
                ->sortBy('id')
                ->first()?->institution?->type ?? '-';

            return [
                'code'       => $report->code,
                'tanggal'    => $report->created_at->format('Y-m-d'),
                'pelapor'    => $pelapor,
                'institusi'  => $institusi,
                'kategori'   => $report->category?->name ?? '-',
                'status'     => $report->status,
            ];
        });
    }

    public function getPercentWithEvidenceSimple()
    {
        $total = Report::count();

        $withEvidence = Report::whereHas('journeys.evidences', function ($query) {
            $query->whereNotNull('file_url')
                ->where('file_url', '<>', '');
        })->count();

        if ($total == 0) return 0;

        return round(($withEvidence / $total) * 100);
    }

}
