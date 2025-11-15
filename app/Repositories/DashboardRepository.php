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
    // =========================================
    // FILTER HELPER
    // =========================================
    private function applyFilters($query, $start, $end)
    {
        if ($start && $end) {
            $query->whereBetween('reports.created_at', [
                $start . ' 00:00:00',
                $end . ' 23:59:59'
            ]);
        }

        return $query;
    }

    // =========================================
    // STATUS SUMMARY
    // =========================================
    public function getStatusSummary($start = null, $end = null)
    {
        $base = Report::query();
        $base = $this->applyFilters($base, $start, $end);

        return [
            'baru' => (clone $base)->where('status','SUBMITTED')->count(),
            'diproses' => (clone $base)->whereIn('status',['PEMERIKSAAN','LIMPAH','SIDANG'])->count(),
            'selesai' => (clone $base)->where('status','SELESAI')->count(),
        ];
    }

    // =========================================
    // TOP CATEGORIES
    // =========================================

    
    public function getTopCategories($start = null, $end = null, $limit = 5)
    {
        $query = Report::query()
            ->join('report_categories', 'reports.category_id', '=', 'report_categories.id');

        $query = $this->applyFilters($query, $start, $end);

        return $query
            ->select('report_categories.name as category', DB::raw('COUNT(reports.id) as total'))
            ->groupBy('report_categories.id', 'report_categories.name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    // =========================================
    // TREND REPORTS
    // =========================================
    public function getTrendReports($start = null, $end = null)
    {
        $query = Report::query();
        $query = $this->applyFilters($query, $start, $end);

        return $query
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }


    // =========================================
    // TOTAL REPORTS
    // =========================================
    public function getTotalReports($start = null, $end = null)
    {
        $query = Report::query();
        $query = $this->applyFilters($query, $start, $end);

        return $query->count();
    }

    // =========================================
    // TOP CATEGORY ACTIVE
    // =========================================
    public function getTopCategoryAktif($start = null, $end = null)
    {
        $query = Report::query()
            ->join('report_categories', 'reports.category_id', '=', 'report_categories.id')
            ->whereIn('reports.status', ['PEMERIKSAAN', 'LIMPAH', 'SIDANG']);

        $query = $this->applyFilters($query, $start, $end);

        return $query
            ->select('report_categories.name as category', DB::raw('COUNT(reports.id) as total'))
            ->groupBy('report_categories.id','report_categories.name')
            ->orderByDesc('total')
            ->first();
    }

    // =========================================
    // LAPORAN AKTIF
    // =========================================
    public function getLaporanAktif($start = null, $end = null)
    {
        $query = Report::query()
            ->whereIn('status', ['PEMERIKSAAN','LIMPAH','SIDANG']);

        $query = $this->applyFilters($query, $start, $end);

        return $query->count();
    }


    // =========================================
    // PERSENTASE SELESAI
    // =========================================
    public function getPersentasiLaporanSelesai($start = null, $end = null)
    {
        $query = Report::query();
        $query = $this->applyFilters($query, $start, $end);
        $total = $query->count();

        $selesai = $query->where('status','SELESAI')->count();

        if ($total == 0) return 0;

        return round(($selesai / $total) * 100);
    }

    // =========================================
    // AVG RESOLUTION TIME
    // =========================================
    public function getAverageResolutionTime($start = null, $end = null)
    {
        $journey = ReportJourney::query()
            ->where('type','SELESAI')
            ->join('reports','reports.id','=','report_journeys.report_id');

        // apply filter ke *reports*
        if ($start && $end) {
            $journey->whereBetween('reports.created_at', [
                $start . ' 00:00:00',
                $end . ' 23:59:59',
            ]);
        }

        $results = $journey
            ->select(DB::raw('TIMESTAMPDIFF(DAY, reports.created_at, report_journeys.created_at) as diff'))
            ->pluck('diff');

        if ($results->isEmpty()) return 0;

        return round($results->avg(), 1);
    }

    // =========================================
    // RECENT REPORTS
    // =========================================
    public function getRecentReports($start = null, $end = null)
    {
        $query = Report::with(['suspects','journeys.institution','category'])
            ->orderBy('created_at','desc')
            ->take(20);

        $query = $this->applyFilters($query, $start, $end);

        return $query->get()->map(function ($report) {
            return [
                'code'       => $report->code,
                'tanggal'    => $report->created_at->format('Y-m-d'),
                'pelapor'    => $report->suspects->first()?->name ?? '-',
                'institusi'  => $report->journeys->first()?->institution?->type ?? '-',
                'kategori'   => $report->category?->name ?? '-',
                'status'     => $report->status,
            ];
        });
    }

    // =========================================
    // WITH EVIDENCE
    // =========================================
    public function getPercentWithEvidenceSimple($start = null, $end = null)
    {
        $query = Report::query();
        $query = $this->applyFilters($query, $start, $end);

        $total = $query->count();

        $withEvidence = $query->whereHas('journeys.evidences', function($q){
            $q->whereNotNull('file_url')->where('file_url','<>','');
        })->count();

        if($total == 0) return 0;

        return round(($withEvidence / $total) * 100);
    }

}
