<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\EventUnitProof;
use App\Models\EventUnitProofFile;
use App\Models\Suspect;
use App\Models\Institution;
use App\Models\ReportCategory;
use App\Models\ReportJourney;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

    private function applyEventFilters($query, $start, $end)
    {
        if ($start && $end) {
            $query->whereBetween('start_at', [
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

        $result = $query
            ->select('report_categories.name as category', DB::raw('COUNT(reports.id) as total'))
            ->groupBy('report_categories.id','report_categories.name')
            ->orderByDesc('total')
            ->first();

        // Jika data kosong
        if (!$result) {
            return [
                'category' => '-',
                'total' => 0,
            ];
        }

        return $result;
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

        // Apply filter ke reports
        if ($start && $end) {
            $journey->whereBetween('reports.created_at', [
                $start . ' 00:00:00',
                $end . ' 23:59:59',
            ]);
        }

        $results = $journey
            ->select(DB::raw('TIMESTAMPDIFF(HOUR, reports.created_at, report_journeys.created_at) as diff')) // per jam
            ->pluck('diff');

        if ($results->isEmpty()) return 0;

        return round($results->avg(), 1); // rata-rata dalam jam
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
                'code'       => $report->code ?? $report->id, 
                'tanggal'    => $report->created_at?->format('Y-m-d') ?? null,
                'pelapor'    => optional($report->suspects->first())->name ?? '-',
                'institusi'  => optional(optional($report->journeys->first())->institution)->type ?? '-',
                'kategori'   => $report->category?->name ?? '-',
                'status'     => $report->status ?? '-',
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



    // ========================================
    // Top Isntitusi by Report Count
    // ========================================

    // public function getTopInstitusi($start = null, $end = null)
    // {
    //     $institutionId = Auth::user()->institution_id;

    //     return DB::table('report_journeys')
    //         ->join('institutions', 'institutions.id', '=', 'report_journeys.institution_id')
    //         ->join('reports', 'reports.id', '=', 'report_journeys.report_id')
    //         ->when($start && $end, function($q) use ($start, $end){
    //             $q->whereBetween('reports.created_at', [
    //                 $start . ' 00:00:00',
    //                 $end . ' 23:59:59'
    //             ]);
    //         })
    //         ->when($institutionId, function($q) use ($institutionId){
    //             $q->where('institutions.id', $institutionId);
    //         })
    //         ->select(
    //             'institutions.name as institution',
    //             DB::raw('COUNT(DISTINCT reports.id) as total')
    //         )
    //         ->groupBy('institutions.id', 'institutions.name')
    //         ->orderByDesc('total')
    //         ->limit(5)
    //         ->get();
    // }


    public function getBacklogPerTahap($start = null, $end = null)
    {
        // Filter report berdasarkan tanggal awal–akhir
        $query = Report::query();
        $query = $this->applyFilters($query, $start, $end);

        $reports = $query->with(['journeys' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])->get();

        $result = [];

        foreach ($reports as $report) {
            $lastJourney = $report->journeys->first(); // journey terbaru

            if (!$lastJourney) {
                continue;
            }

            // Hitung berapa hari laporan STUCK di tahap terakhir
            $days = $lastJourney->created_at->diffInDays(now());

            $result[] = [
                'report_id'     => $report->code,
                'report_id_raw' => $report->id,
                'tahap'         => $lastJourney->type,          // nama tahap
                'durasi'        => $days,                       // durasi stuck
            ];
        }

        return $result;
    }

    // =========================================
    // EVENT DASHBOARD
    // =========================================
    public function getEventSummary($start = null, $end = null): array
    {
        $eventsQuery = Event::query();
        $eventsQuery = $this->applyEventFilters($eventsQuery, $start, $end);

        $totalEvents = $eventsQuery->count();
        $totalParticipants = EventParticipant::query()
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereHas('event', function ($e) use ($start, $end) {
                    $e->whereBetween('start_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
                });
            })
            ->count();

        $totalFiles = EventUnitProofFile::query()
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereHas('eventUnitProof', function ($p) use ($start, $end) {
                    $p->whereHas('event', function ($e) use ($start, $end) {
                        $e->whereBetween('start_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
                    });
                });
            })
            ->count();

        return [
            'total_events' => $totalEvents,
            'total_participants' => $totalParticipants,
            'total_files' => $totalFiles,
        ];
    }

    public function getRecentEvents($start = null, $end = null, $limit = 5)
    {
        $events = Event::withCount('participants')
            ->orderByDesc('id')
            ->take($limit);

        $events = $this->applyEventFilters($events, $start, $end)->get();

        return $events->map(function ($ev) {
            $headerIds = EventUnitProof::where('event_id', $ev->id)->pluck('id');
            $filesCount = $headerIds->isNotEmpty()
                ? EventUnitProofFile::whereIn('event_unit_proof_id', $headerIds)->count()
                : 0;
            $uploadedDivisions = EventUnitProof::where('event_id', $ev->id)->pluck('division_id')->filter()->unique();
            $participantsTotal = (int) ($ev->participants_count ?? 0);
            $progressPct = $participantsTotal > 0 ? round(($uploadedDivisions->count() / $participantsTotal) * 100) : 0;

            return [
                'id' => $ev->id,
                'name' => $ev->name,
                'location' => $ev->location,
                'start_at' => optional($ev->start_at)->format('d M Y H:i'),
                'participants_total' => $participantsTotal,
                'files_count' => $filesCount,
                'progress_pct' => $progressPct,
                'uuid' => $ev->uuid,
            ];
        });
    }

}
