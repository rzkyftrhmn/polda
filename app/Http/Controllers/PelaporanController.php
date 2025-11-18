<?php

namespace App\Http\Controllers;

use App\Enums\ReportJourneyType;
use App\Models\Division;
use App\Models\Institution;
use App\Services\PelaporanService;
use App\Repositories\PelaporanRepository;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use App\Models\ReportCategory;
use App\Models\Report;
use App\Services\ReportJourneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelaporanController extends Controller
{
    protected $service, $repository, $journeyService,$feature_title, $feature_name, $feature_path, $user;

    public function __construct(PelaporanRepository $repository, PelaporanService $service,ReportJourneyService $journeyService)
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->journeyService = $journeyService;
        $this->feature_title = 'Pelaporan';
        $this->feature_name = 'Pelaporan';
        $this->feature_path = 'pelaporan';

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    /** Menampilkan halaman utama */
    public function index()
    {
        return view('pages.pelaporan.index', [
            'title' => $this->feature_title,
            'name' => $this->feature_name,
            'path' => $this->feature_path,
            'user' => $this->user,
        ]);
    }

    /** DataTables server-side */
    public function datatables(Request $request)
    {
        if (!$request->ajax()) return;

        $columns = ['id', 'title', 'incident_datetime', 'status', 'action'];

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColIndex = $request->input('order.0.column', 1);
        $dir = $request->input('order.0.dir', 'asc');
        $order = $columns[$orderColIndex] ?? 'created_at';
        $search = $request->input('search.value', '');
        $filter_q = $request->input('filter_q', '');

        $query = $this->service->datatables($filter_q);

        // Jika ada search bawaan DataTables
        if (!empty($search)) {
            $query = $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%");
            });
        }
        
        $total = $query->count();

        $reports = $query->orderBy($order, $dir)
                 ->skip($start)
                 ->take($limit)
                 ->get();

        $data = [];
        foreach ($reports as $key => $report) {
            $htmlButton = '<td class="text-nowrap">
                <a href="' . route('pelaporan.show', $report->id) . '" class="btn btn-info btn-sm content-icon btn-detail">
                    <i class="fa fa-eye"></i>
                </a>
                <a href="' . route('pelaporan.edit', $report->id) . '" class="btn btn-warning btn-sm content-icon btn-edit" data-id="' . $report->id . '">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="javascript:void(0);" 
                    class="btn btn-danger btn-sm content-icon btn-delete"
                    data-id="' . $report->id . '"
                    data-name="' . htmlspecialchars($report->title ?? '', ENT_QUOTES) . '"
                    data-url="' . route('pelaporan.destroy', $report->id) . '"
                    data-title="Hapus Laporan?">
                    <i class="fa fa-times"></i>
                </a>
            </td>';

            $data[] = [
                'DT_RowIndex' => $key + 1 + $start,
                'title' => $report->title,
                'incident_datetime' => $report->incident_datetime 
                    ? \Carbon\Carbon::parse($report->incident_datetime)->format('d-m-Y') 
                    : '-',
                'status' => $report->status,
                'action' => $htmlButton,
            ];
        }


        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }

    /** Form tambah laporan */
    public function create()
    {
        return view('pages.pelaporan.create', [
            'title' => 'Tambah Laporan',
            'pelaporan' => null,
            'provinces' => Province::all(),
            'cities' => [],
            'districts' => [],
            'categories' => ReportCategory::all(),
        ]);
    }

    /** Ambil cities per province */
    public function getCitiesByProvince($provinceId)
    {
        $province = Province::find($provinceId);
        return $province ? response()->json($province->cities) : response()->json([], 404);
    }

    /** Ambil districts per city */
    public function getDistrictsByCity($cityId)
    {
        $city = City::find($cityId);
        return $city ? response()->json($city->districts) : response()->json([], 404);
    }

    /** Simpan laporan baru */
    public function store(Request $request)
    {
        // dd($request->suspects);

        $validated = $request->validate([
            'title'                => 'required|string',
            'incident_datetime'    => 'required|date',
            'category_id'          => 'required|integer',
            'description'            => 'required|string',

            'city_id'              => 'required|integer',
            'district_id'          => 'required|integer',
            'address_detail'       => 'nullable|string',

            'name_of_reporter'     => 'required|string',
            'address_of_reporter'  => 'required|string',
            'phone_of_reporter'    => 'required|string',

            'suspects'             => 'nullable|array',
            'suspects.*.name'      => 'required_with:suspects|string',
            'suspects.*.division_id' => 'nullable|integer',
        ]);
        $report = $this->service->store($validated);
        return redirect()->route('pelaporan.index')
                 ->with('success', 'Laporan Berhasil Dibuat.');

    }

    public function byType()
    {
        $type = request('type'); 

        return Division::where('type', $type)->get();
    }

    /** Form edit laporan */
    public function edit($id)
    {
        $report = $this->service->getById($id);
        if (!$report) return redirect()->route('pelaporan.index')->with('error', 'Laporan tidak ditemukan.');

        return view('pages.pelaporan.create', [
            'title' => 'Edit Laporan',
            'pelaporan' => $report,
            'provinces' => Province::all(),
            'cities' => $report->province_id ? City::where('province_code', $report->province_id)->get() : [],
            'districts' => $report->city_id ? District::where('city_code', $report->city_id)->get() : [],
            'categories' => ReportCategory::all(),
        ]);
    }

    /** Update laporan */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'incident_datetime' => 'required|date',
            'province_id' => 'required|integer',
            'city_id' => 'required|integer',
            'district_id' => 'required|integer',
            'category_id' => 'required|integer',
            'address_detail' => 'nullable|string',
            'suspects.*.name' => 'required|string',
            'suspects.*.description' => 'nullable|string',
        ]);

        $request->merge([
            'status' => $request->input('status', 'SUBMITTED')
        ]);
        $report = $this->service->update($id, $validated);

        if (!$report) {
            return back()->with('error', 'Gagal memperbarui laporan')->withInput();
        }

        return redirect()->route('pelaporan.show', $report->id)
                 ->with('success', 'Laporan Berhasil Diupdate.');
    }


    /** Tampilkan detail laporan */
   /** Tampilkan detail laporan + timeline journey */
    public function show($id)
    {
        $report = Report::with(['category', 'province', 'city', 'district', 'suspects.division', 'accessDatas'])
            ->findOrFail($id);

        $journeys = $this->journeyService->paginateByReport($report->id, 5, order: 'desc');

        $institutions = Institution::orderBy('name')->get(['id', 'name']);
        $divisions = Division::with('parent')
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'parent_id', 'permissions']);

        $journeyTypes = ReportJourneyType::manualOptions();

        $user = auth()->user();
        $division = $user?->division;
        $hasAccess = $division
            ? $report->accessDatas
                ->where('division_id', $division->id)
                ->where('is_finish', false)
                ->isNotEmpty()
            : false;

        $canInspection = $hasAccess && ($division?->canInspection() ?? false);
        $canInvestigation = $hasAccess && ($division?->canInvestigation() ?? false);

        return view('pages.pelaporan.show', [
            'report' => $report,
            'journeys' => $journeys,
            'journeyTypes' => $journeyTypes,
            'institutions' => $institutions,
            'divisions' => $divisions,
            'statusLabel' => ReportJourneyType::tryFrom($report->status)?->label() ?? $report->status,
            'canInspection' => $canInspection,
            'canInvestigation' => $canInvestigation,
            'hasAccess' => $hasAccess,
        ]);
    }

    /** Hapus laporan */
    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (request()->ajax()) {
            return response()->json([
                'status' => $deleted,
                'message' => $deleted ? 'Laporan berhasil dihapus' : 'Gagal menghapus laporan'
            ]);
        }

        return $deleted
            ? redirect()->route('pelaporan.index')->with('success', 'Laporan berhasil dihapus')
            : back()->with('error', 'Gagal menghapus laporan');
    }
    
}
