<?php

namespace App\Http\Controllers;

use App\Services\PelaporanService;
use App\Repositories\PelaporanRepository;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use App\Models\ReportCategory;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelaporanController extends Controller
{
    protected $service, $repository, $feature_title, $feature_name, $feature_path, $user;

    public function __construct(PelaporanRepository $repository, PelaporanService $service)
    {
        $this->repository = $repository;
        $this->service = $service;

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
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'incident_datetime' => 'required|date',
            'province_id' => 'required|integer',
            'city_id' => 'required|integer',
            'district_id' => 'required|integer',
            'category_id' => 'required|integer',
            'address_detail' => 'nullable|string',
            'status' => 'nullable|string',
            'suspects' => 'nullable|array',
            'suspects.*.name' => 'required|string',
            'suspects.*.description' => 'nullable|string',
        ]);

        // Set default status jika tidak dikirim dari form
        if (empty($validated['status'])) {
            $validated['status'] = 'SUBMITTED';
        }

        // Generate kode otomatis
        $bulanTahun = now()->format('my'); // misal November 2025 → 1125
        $lastReport = Report::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->latest()->first();
        $noUrut = $lastReport ? (intval(substr($lastReport->code, -4)) + 1) : 1;
        $validated['code'] = 'RPT-'.$bulanTahun.str_pad($noUrut, 4, '0', STR_PAD_LEFT);

        // Simpan laporan
        $report = $this->service->store($validated);

        if (!$report) {
            return back()->with('error', 'Gagal membuat laporan')->withInput();
        }

        return redirect()->route('pelaporan.index')->with('success', 'Laporan berhasil dibuat.');
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

        return redirect()->route('pelaporan.index')->with('success', 'Laporan berhasil diperbarui.');
    }


    /** Tampilkan detail laporan */
    public function show($id)
    {
        $report = $this->service->getById($id);
        if (!$report) {
            return redirect()->route('pelaporan.index')->with('error', 'Laporan tidak ditemukan.');
        }

        return view('pages.pelaporan.show', [
            'title' => 'Detail Laporan',
            'report' => $report,
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
