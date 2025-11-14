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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.index');
    }

    public function statusSummary()
    {
        $statusSummary = $this->dashboardRepo->getStatusSummary();
        return response()->json($statusSummary);
    }
    
    public function topCategories()
    {
        $topCategories = $this->dashboardRepo->getTopCategories();
        return response()->json($topCategories);
    }

    public function getTrendReports()
    {
        $data = $this->dashboardRepo->getTrendReports();

        return response()->json($data);
    }

    public function getTotalReports()
    {
        $total = $this->dashboardRepo->getTotalReports();

        return response()->json([
            'total' => $total
        ]);
    }

    public function getTopCategoryAktif()
    {
        $top = $this->dashboardRepo->getTopCategoryAktif();

        return response()->json([
            'category' => $top->category ?? '-',
            'total' => $top->total ?? 0
        ]);
    }

    public function getLaporanAktif()
    {
        $aktif = $this->dashboardRepo->getLaporanAktif();

        return response()->json([
            'aktif' => $aktif
        ]);
    }

    public function getPersentasiLaporanSelesai()
    {
        $rate = $this->dashboardRepo->getPersentasiLaporanSelesai();
        return response()->json(['rate' => $rate]);
    }

   
}
