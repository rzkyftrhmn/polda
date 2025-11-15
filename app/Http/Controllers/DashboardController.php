<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DashboardRepository;

class DashboardController extends Controller
{
    protected $dashboardRepo;

    public function __construct(DashboardRepository $dashboardRepo)
    {
        $this->dashboardRepo = $dashboardRepo;
    }

    public function index()
    {
        return view('pages.index'); // sudah tidak butuh institutions
    }

    public function statusSummary(Request $request)
    {
        return response()->json(
            $this->dashboardRepo->getStatusSummary(
                $request->start_date,
                $request->end_date
            )
        );
    }


    public function getTrendReports(Request $request)
    {
        return response()->json(
            $this->dashboardRepo->getTrendReports(
                $request->start_date,
                $request->end_date
            )
        );
    }


    public function getTotalReports(Request $request)
    {
        return response()->json([
            'total' => $this->dashboardRepo->getTotalReports(
                $request->start_date,
                $request->end_date
            )
        ]);
    }

    public function getTopCategoryAktif(Request $request)
    {
        return response()->json(
            $this->dashboardRepo->getTopCategoryAktif(
                $request->start_date,
                $request->end_date
            )
        );
    }

    public function getLaporanAktif(Request $request)
    {
        return response()->json([
            'aktif' => $this->dashboardRepo->getLaporanAktif(
                $request->start_date,
                $request->end_date
            )
        ]);
    }

    public function getPersentasiLaporanSelesai(Request $request)
    {
        return response()->json([
            'rate' => $this->dashboardRepo->getPersentasiLaporanSelesai(
                $request->start_date,
                $request->end_date
            )
        ]);
    }

    public function getAvgResolution(Request $request)
    {
        return response()->json([
            'avg_resolution_time' => $this->dashboardRepo->getAverageResolutionTime(
                $request->start_date,
                $request->end_date
            )
        ]);
    }

    public function recentReports(Request $request)
    {
        return response()->json(
            $this->dashboardRepo->getRecentReports(
                $request->start_date,
                $request->end_date
            )
        );
    }

    public function kpiWithEvidence(Request $request)
    {
        return response()->json([
            'rate' => $this->dashboardRepo->getPercentWithEvidenceSimple(
                $request->start_date,
                $request->end_date
            )
        ]);
    }
}
