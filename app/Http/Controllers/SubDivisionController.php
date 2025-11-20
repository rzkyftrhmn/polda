<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use App\Repositories\SubDivisionRepository;
use App\Services\SubDivisionService;

class SubDivisionController extends Controller
{
    protected $subDivisionRepository, $service, $user;

    public function __construct(SubDivisionRepository $subDivisionRepository, SubDivisionService $service)
    {
        $this->subDivisionRepository = $subDivisionRepository;
        $this->service = $service;

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
    }

    public function index()
    {
        return view('pages.subdivisions.index', [
            'title' => 'Sub Divisi',
            'user' => $this->user,
        ]);
    }

    public function datatables(Request $request)
    {
        if ($request->ajax()) {
            $columns = [null, 'name', 'type', null];
            $limit = $request->input('length');
            $start = $request->input('start');
            $orderColIndex = $request->input('order.0.column');
            $dir = $request->input('order.0.dir', 'asc');
            $order = $columns[$orderColIndex] ?? 'created_at';
            $filter_q = $request->input('filter_q');

            $query = $this->subDivisionRepository->getDataTableQuery();

            if (!empty($filter_q)) {
                $query->where('name', 'like', "%{$filter_q}%");
            }

            $totalData = $query->count();

            if ($order) $query->orderBy($order, $dir);

            $subdivisions = $query->skip($start)->take($limit)->get();

            $data = [];
            foreach ($subdivisions as $key => $subdivision) {
                $parentName = $subdivision->parent ? $subdivision->parent->name : '-';
                $permissions = [];
                if (!empty($subdivision->permissions)) {
                    $decoded = json_decode($subdivision->permissions, true);
                    if (is_array($decoded)) {
                        $permissions = $decoded;
                    }
                }

                $htmlButton = '
                    <td class="text-nowrap">
                        <a href="javascript:void(0);" 
                           class="btn btn-info btn-sm content-icon btn-detail"
                           data-id="' . $subdivision->id . '"
                           data-name="' . htmlspecialchars($subdivision->name, ENT_QUOTES) . '"
                           data-parent="' . htmlspecialchars($parentName, ENT_QUOTES) . '"
                           data-level="' . htmlspecialchars(strtolower($subdivision->level ?? ''), ENT_QUOTES) . '"
                           data-type="' . htmlspecialchars($subdivision->type ?? '', ENT_QUOTES) . '"
                           data-permissions="' . htmlspecialchars(json_encode($permissions), ENT_QUOTES) . '"
                           data-created_at="' . htmlspecialchars($subdivision->created_at?->format('d-m-Y H:i') ?? '-', ENT_QUOTES) . '">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="' . route('subdivisions.edit', $subdivision->id) . '" class="btn btn-warning btn-sm content-icon">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-danger btn-sm content-icon btn-delete"
                            data-id="' . $subdivision->id . '"
                            data-name="' . htmlspecialchars($subdivision->name, ENT_QUOTES) . '"
                            data-url="' . route('subdivisions.destroy', $subdivision->id) . '"
                            data-title="Hapus Sub Divisi?">
                            <i class="fa fa-times"></i>
                        </a>
                    </td>';

                $data[] = [
                    'DT_RowIndex' => $key + 1 + $start,
                    'name' => $subdivision->name,
                    'type' => $subdivision->type,
                    'parent' => $parentName,
                    'created_at' => $subdivision->created_at?->format('d-m-Y') ?? '-',
                    'action' => $htmlButton,
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalData,
                'data' => $data,
            ]);
        }
    }

    public function create()
    {
        $parentDivisions = Division::whereNull('parent_id')->orderBy('name')->get();
        return view('pages.subdivisions.create', [
            'title' => 'Tambah Sub Divisi',
            'subdivision' => null,
            'parentDivisions' => $parentDivisions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:divisions,name',
            'level' => 'required|string|in:polda,polres,polsek',
            'type' => 'required|string|in:satker,satwil',
            'permissions.inspection' => 'nullable|boolean',
            'permissions.investigation' => 'nullable|boolean',
        ]);

        $data = [
            'parent_id' => $request->input('parent_id'),
            'name' => $validated['name'],
            'level' => strtolower($validated['level']),
            'type' => $validated['type'],
            'permissions' => json_encode([
                'inspection' => (bool) ($request->input('permissions.inspection') ?? false),
                'investigation' => (bool) ($request->input('permissions.investigation') ?? false),
            ]),
        ];

        $result = $this->service->store($data);

        if ($result['status']) {
            return redirect()->route('subdivisions.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    public function edit($id)
    {
        $subdivision = $this->subDivisionRepository->findById($id);
        $parentDivisions = Division::whereNull('parent_id')->orderBy('name')->get();

        return view('pages.subdivisions.create', [
            'title' => 'Edit Sub Divisi',
            'subdivision' => $subdivision,
            'parentDivisions' => $parentDivisions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'parent_id' => 'required|integer|exists:divisions,id',
            'name' => 'required|unique:divisions,name,' . $id,
            'level' => 'required|string|in:polda,polres,polsek',
            'type' => 'required|string|in:satker,satwil',
            'permissions.inspection' => 'nullable|boolean',
            'permissions.investigation' => 'nullable|boolean',
        ]);

        $data = [
            'parent_id' => $validated['parent_id'],
            'name' => $validated['name'],
            'level' => strtolower($validated['level']),
            'type' => $validated['type'],
            'permissions' => json_encode([
                'inspection' => (bool) ($request->input('permissions.inspection') ?? false),
                'investigation' => (bool) ($request->input('permissions.investigation') ?? false),
            ]),
        ];

        $result = $this->service->update($id, $data);

        if ($result['status']) {
            return redirect()->route('subdivisions.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    public function destroy($id)
    {
        $result = $this->service->delete($id);

        if (request()->ajax()) {
            return response()->json([
                'status' => $result,
                'message' => $result ? 'Unit berhasil dihapus' : 'Gagal menghapus Unit',
            ]);
        }

        return redirect()->route('subdivisions.index')->with('success', $result ? 'Unit berhasil dihapus' : 'Gagal menghapus Unit');
    }
}
