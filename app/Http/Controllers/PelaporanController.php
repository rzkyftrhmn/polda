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
        $query = $this->service->datatables($request->input('filter_q', ''));

        $search = $request->input('search.value', '');
        if (!empty($search)) {
            $query = $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            });
        }

        $total = $query->count();

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColIndex = $request->input('order.0.column', 1);
        $dir = $request->input('order.0.dir', 'asc');
        $order = ['id', 'title', 'incident_datetime', 'status', 'action'][$orderColIndex] ?? 'created_at';

        $filter = $request->input('filter_q', '');

        if (!empty($filter)) {
            $query = $query->where('title', 'like', "%$filter%");
        }

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
        $division = auth()->user()->division;

        if (!$division) {
            return redirect()->route('pelaporan.index')
                ->with('error', 'Divisi anda tidak valid. Hubungi admin.');
        }

        //error fix perrmission check
        $perm = $division->permissions;
        if (is_string($perm)) {
            $decoded = json_decode($perm, true);
            $perm = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($perm)) {
            $perm = [];
        }

        if (($perm['inspection'] ?? false) === false && ($perm['investigation'] ?? false) === false) {
            return redirect()->route('pelaporan.index')
                ->with('error', 'Anda tidak memiliki izin untuk membuat laporan.');
        }

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
        return redirect()->route('pelaporan.show', $report->id)
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

        $division  = auth()->user()->division;
        $perm      = json_decode($division->permissions, true);

        if (($perm['inspection'] ?? false) === false && ($perm['investigation'] ?? false) === false) {
            return redirect()->route('pelaporan.index')
                ->with('error', 'Anda tidak memiliki izin untuk membuat laporan.');
        }
        
        $report = $this->service->getById($id);
        // dd($report->suspects->toArray());
        if (!$report) return redirect()->route('pelaporan.index')
            ->with('error', 'Laporan tidak ditemukan.');

        return view('pages.pelaporan.create', [
            'title' => 'Edit Laporan',
            'pelaporan' => $report,
            'provinces' => Province::all(),
            'cities' => $report->province_id 
                ? City::where('province_code', $report->province_id)->get()
                : [],

            'districts' => $report->city_id 
                ? District::where('city_code', $report->city_id)->get()
                : [],
            'categories' => ReportCategory::all(),
        ]);
    }




    /** Update laporan */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validated = $request->validate([
            'title'               => 'required|string',
            'incident_datetime'   => 'required|date',
            'category_id'         => 'required|integer',
            'description'         => 'required|string',
            'city_id'             => 'required|integer',
            'district_id'         => 'required|integer',
            'address_detail'      => 'nullable|string',
            'name_of_reporter'    => 'required|string',
            'address_of_reporter' => 'required|string',
            'phone_of_reporter'   => 'required|string',
            'suspects'            => 'nullable|array',
            'suspects.*.name'     => 'required_with:suspects',
            'suspects.*.division_id' => 'nullable|integer',
        ]);

        $this->service->update($id, $validated);


        return redirect()->route('pelaporan.show', $id)
                 ->with('success', 'Laporan Berhasil Diperbaharui.');
    }


    public function show($id)
    {
        $report = Report::with([
            'category',
            'province',
            'city',
            'district',
            'suspects.division',
            'accessDatas',
            'creator',
        ])->findOrFail($id);

        // Pastikan akses awal creator dibuat
        $this->journeyService->ensureInitialAccess($report);

        // Ambil timeline journey
        $journeys = $this->journeyService->paginateByReport(
            reportId: $report->id,
            perPage: 5,
            order: 'desc'
        );

        // Data referensi dropdown
        $institutions = Institution::orderBy('name')->get(['id', 'name']);
        $divisions = Division::with('parent')
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'parent_id', 'permissions']);

        $investigationDivisions = $divisions
            ->filter(fn ($division) => $division->canInvestigation())
            ->values();
        
        $journeyTypes = ReportJourneyType::manualOptions();

        // User & admin check
        $user = auth()->user();
        $division = $user?->division;

        $isAdmin = $user && method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole([ROLE_ADMIN])
            : false;

        // Cek akses
        $hasAccess = $this->journeyService->hasAccess(
            division: $division,
            report: $report,
            isAdmin: $isAdmin
        );

        // Logic form mana yang boleh muncul
        $canInspection = $division?->canInspection() ?? false;
        $canInvestigation = $division?->canInvestigation() ?? false;

        $showInspectionForm = $hasAccess && $canInspection;
        $showInvestigationForm = $hasAccess && !$showInspectionForm && $canInvestigation;

        // Tab progress muncul kalau user boleh update
        $showProgressTab = ($isAdmin || $showInspectionForm || $showInvestigationForm)
            && $report->status !== ReportJourneyType::COMPLETED->value;

        $defaultFlow = $showInspectionForm ? 'inspection' : 'investigation';
        $inspectionPrefill = $this->journeyService->latestInspectionPrefill($report);
        $inspectionEvidence = $this->journeyService->latestInspectionEvidence($report);
        $trialPrefill = $this->journeyService->latestTrialPrefill($report);
        $adminDocuments = $this->journeyService->adminDocumentsPrefill($report);
        $trialEvidence = $this->journeyService->latestTrialEvidence($report);

        $relatedUserOptions = $this->service->getRelatedUsersForReport($report);
        $instructions = $this->service->getInstructionsForReport($report->id);
        $userMap = $this->service->getUserMapForInstructions($instructions);

        return view('pages.pelaporan.show', [
            'report' => $report,
            'journeys' => $journeys,
            'journeyTypes' => $journeyTypes,
            'institutions' => $institutions,
            'divisions' => $divisions,
            'investigationDivisions' => $investigationDivisions,
            'defaultFlow' => $defaultFlow,
            'statusLabel' => ReportJourneyType::tryFrom($report->status)?->label() ?? $report->status,
            'hasAccess' => $hasAccess,
            'showInspectionForm' => $showInspectionForm,
            'showInvestigationForm' => $showInvestigationForm,
            'showProgressTab' => $showProgressTab,
            'inspectionPrefill' => $inspectionPrefill,
            'inspectionEvidence' => $inspectionEvidence,
            'trialPrefill' => $trialPrefill,
            'trialEvidence' => $trialEvidence,
            'adminDocuments' => $adminDocuments,
            'relatedUserOptions' => $relatedUserOptions,
            'instructions' => $instructions,
            'userMap' => $userMap,
            'instructionStoreUrl' => route('reports.instructions.store', $report->id),
        ]);
    }

    public function storeInstruction(Request $request, Report $report)
    {
        $user = auth()->user();
        $division = $user?->division;
        $isAdmin = $user && method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole([ROLE_ADMIN])
            : false;

        $hasAccess = $this->journeyService->hasAccess($division, $report, $isAdmin);
        if (!$hasAccess) {
            return response()->json(['message' => 'Tidak memiliki akses untuk membuat instruksi.'], 403);
        }

        $validated = $request->validate([
            'user_id_to' => 'required|integer',
            'message' => 'required|string',
        ]);

        $instruction = $this->service->storeInstruction(
            $report->id,
            $user->id,
            (int) $validated['user_id_to'],
            $validated['message']
        );

        $fromName = $user->name ?? 'Anda';
        $toUser = $report->accessDatas->pluck('division.users')->flatten(1)->firstWhere('id', (int) $validated['user_id_to']);
        if (!$toUser) {
            $toUser = \App\Models\User::find((int) $validated['user_id_to']);
        }

        return response()->json([
            'id' => $instruction->id,
            'created_at' => optional($instruction->created_at)->format('d M Y H:i'),
            'from_name' => $fromName,
            'to_name' => $toUser?->name ?? '-',
            'message' => $instruction->message,
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
