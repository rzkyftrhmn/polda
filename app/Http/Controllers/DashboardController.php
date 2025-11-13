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

   
}
