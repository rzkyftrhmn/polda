<?php

namespace App\Http\Controllers;

use App\Enums\ReportJourneyType;
use App\Exports\ReportDataExcelExport;
use App\Http\Requests\ReportDataFilterRequest;
use App\Http\Resources\ReportDataResource;
use App\Models\ReportCategory;
use App\Services\ReportDataService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\City;
use App\Models\District;
use App\Models\Province;

class ReportDataController extends Controller
{
    protected ReportDataService $service;
    protected string $feature_title;
    protected string $feature_name;
    protected string $feature_path;
    protected $user;

    public function __construct(ReportDataService $service)
    {
        $this->service = $service;
        $this->feature_title = 'Report Data';
        $this->feature_name = 'Report Data';
        $this->feature_path = 'report-data';

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $categories = ReportCategory::orderBy('name')->get();
        $statuses = collect(ReportJourneyType::cases())->map(fn (ReportJourneyType $type) => [
            'value' => $type->value,
            'label' => $type->label(),
        ]);
        $provinces = Province::where('id', 12)->get();

        return view('pages.report-data.index', [
            'title' => $this->feature_title,
            'name' => $this->feature_name,
            'path' => $this->feature_path,
            'user' => $this->user,
            'categories' => $categories,
            'statuses' => $statuses,
            'provinces' => $provinces,
        ]);
    }

    public function datatables(ReportDataFilterRequest $request): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $filters = $request->filters();

        $columnMap = [
            1 => 'code',
            2 => 'title',
            3 => 'title', // category column fallback
            4 => 'status',
            5 => 'incident_datetime',
            9 => 'created_at',
            10 => 'finish_time',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 9);
        $orderDirection = strtolower($request->input('order.0.dir', $filters['sort_dir'] ?? 'desc'));

        if (empty($filters['sort_by']) && isset($columnMap[$orderColumnIndex])) {
            $filters['sort_by'] = $columnMap[$orderColumnIndex];
        }

        if (empty($filters['sort_dir'])) {
            $filters['sort_dir'] = $orderDirection;
        }

        $totalRecords = $this->service->totalCount();
        $filteredCount = $this->service->filteredCount($filters);

        $length = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);

        $query = $this->service->buildQuery($filters);

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $reports = $query->get();
        $data = ReportDataResource::collection($reports)->resolve();

        // add index column manually for DataTables display
        $data = array_map(function (array $row, int $index) use ($start) {
            return array_merge(['DT_RowIndex' => $start + $index + 1], $row);
        }, $data, array_keys($data));

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredCount,
            'data' => $data,
        ]);
    }

    public function exportExcel(ReportDataFilterRequest $request)
    {
        $filters = $request->filters();
        $filters['sort_by'] = $filters['sort_by'] ?? 'created_at';
        $filters['sort_dir'] = $filters['sort_dir'] ?? 'desc';

        $filename = 'report-data-' . now()->format('Ymd-Hi') . '.xlsx';

        $export = new ReportDataExcelExport($this->service, $filters);

        return $export->download($filename);
    }

    public function exportPdf(ReportDataFilterRequest $request)
    {
        $filters = $request->filters();
        $filters['sort_by'] = $filters['sort_by'] ?? 'created_at';
        $filters['sort_dir'] = $filters['sort_dir'] ?? 'desc';

        $reports = $this->service->buildQuery($filters)->get();
        $resources = ReportDataResource::collection($reports)->resolve();

        $pdf = Pdf::loadView('report-data.pdf', [
            'reports' => $resources,
            'filters' => $this->formatFilterSummary($filters),
            'generatedAt' => now(),
        ])->setPaper('A4', 'landscape');

        $filename = 'report-data-' . now()->format('Ymd-Hi') . '.pdf';

        return $pdf->download($filename);
    }

    public function getCitiesByProvince($provinceId)
    {
        $province = Province::find($provinceId);
        return $province ? response()->json($province->cities) : response()->json([], 404);
    }

    public function getDistrictsByCity($cityId)
    {
        $city = City::find($cityId);
        return $city ? response()->json($city->districts) : response()->json([], 404);
    }

    protected function formatFilterSummary(array $filters): array
    {
        $summary = [];

        if (!empty($filters['q'])) {
            $summary['Pencarian'] = $filters['q'];
        }

        if (!empty($filters['status'])) {
            $statusEnum = ReportJourneyType::tryFrom($filters['status']);
            $summary['Status'] = $statusEnum ? $statusEnum->label() : $filters['status'];
        }

        if (!empty($filters['category_id'])) {
            $category = ReportCategory::find($filters['category_id']);
            $summary['Kategori'] = $category?->name;
        }

        if (!empty($filters['province_id'])) {
            $province = Province::find($filters['province_id']);
            $summary['Provinsi'] = $province?->name;
        }

        if (!empty($filters['city_id'])) {
            $city = City::find($filters['city_id']);
            $summary['Kota/Kabupaten'] = $city?->name;
        }

        if (!empty($filters['district_id'])) {
            $district = District::find($filters['district_id']);
            $summary['Kecamatan'] = $district?->name;
        }

        if (!empty($filters['incident_from']) || !empty($filters['incident_to'])) {
            $summary['Rentang Kejadian'] = $this->formatDateRange($filters['incident_from'] ?? null, $filters['incident_to'] ?? null);
        }

        if (!empty($filters['created_from']) || !empty($filters['created_to'])) {
            $summary['Rentang Dibuat'] = $this->formatDateRange($filters['created_from'] ?? null, $filters['created_to'] ?? null);
        }

        if (!empty($filters['finish_from']) || !empty($filters['finish_to'])) {
            $summary['Rentang Selesai'] = $this->formatDateRange($filters['finish_from'] ?? null, $filters['finish_to'] ?? null);
        }

        if (!empty($filters['sort_by'])) {
            $summary['Urut Berdasarkan'] = $this->formatSortLabel($filters['sort_by'], $filters['sort_dir'] ?? 'desc');
        }

        return array_filter($summary, fn ($value) => !is_null($value) && $value !== '');
    }

    protected function formatDateRange(?string $from, ?string $to): string
    {
        $fromLabel = $from ? Carbon::parse($from)->format('d/m/Y') : '-';
        $toLabel = $to ? Carbon::parse($to)->format('d/m/Y') : '-';

        return $fromLabel . ' - ' . $toLabel;
    }

    protected function formatSortLabel(string $sortBy, string $direction): string
    {
        $labels = [
            'created_at' => 'Tanggal Dibuat',
            'incident_datetime' => 'Tanggal Kejadian',
            'finish_time' => 'Tanggal Selesai',
            'status' => 'Status',
            'code' => 'Kode',
            'title' => 'Judul',
        ];

        $directionLabel = strtolower($direction) === 'asc' ? 'Naik' : 'Turun';

        return ($labels[$sortBy] ?? $sortBy) . ' (' . $directionLabel . ')';
    }
}
