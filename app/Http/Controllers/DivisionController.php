<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DivisionRepository;
use App\Services\DivisionService;

class DivisionController extends Controller
{
    protected $divisionRepository, $service, $user;

    public function __construct(DivisionRepository $divisionRepository, DivisionService $service)
    {
        $this->divisionRepository = $divisionRepository;
        $this->service = $service;

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
    }

    public function index()
    {
        return view('pages.divisions.index', [
            'title' => 'Divisi',
            'user' => $this->user,
        ]);
    }

    public function datatables(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                null,
                'name',
                'type',
                null,
            ];

            $limit = $request->input('length');
            $start = $request->input('start');
            $orderColIndex = $request->input('order.0.column');
            $dir = $request->input('order.0.dir', 'asc');
            $order = $columns[$orderColIndex] ?? 'created_at';
            $filter_q = $request->input('filter_q');

            $query = $this->divisionRepository->getDataTableQuery();

            if (!empty($filter_q)) {
                $query->where('name', 'like', "%{$filter_q}%");
            }

            $search = $request->input('search.value');
            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%");
            }

            $totalData = $query->count();

            if ($order) {
                $query->orderBy($order, $dir);
            }

            $divisions = $query->skip($start)->take($limit)->get();

            $data = [];
            foreach ($divisions as $key => $division) {
                $parentName = $division->parent ? $division->parent->name : '-';
                $htmlButton = '<td class="text-nowrap">
                    <a href="' . route('divisions.edit', $division->id) . '" class="btn btn-warning btn-sm content-icon">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="javascript:void(0);" 
                        class="btn btn-danger btn-sm content-icon btn-delete"
                        data-id="' . $division->id . '"
                        data-name="' . htmlspecialchars($division->name, ENT_QUOTES) . '"
                        data-url="' . route('divisions.destroy', $division->id) . '"
                        data-title="Hapus Divisi?">
                        <i class="fa fa-times"></i>
                    </a>
                </td>';

                $data[] = [
                    'DT_RowIndex' => $key + 1 + $start,
                    'name' => $division->name,
                    'type' => $division->type,
                    'created_at' => $division->created_at ? $division->created_at->format('d-m-Y') : '-',
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
        $parentDivisions = $this->divisionRepository->getAllOrderedByName();
        return view('pages.divisions.create', [
            'title' => 'Tambah Divisi',
            'division' => null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:divisions,name',
            'type' => 'required',
        ]);

        $result = $this->service->store($request->all());

        if ($result['status']) {
            return redirect()->route('divisions.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    public function edit($id)
    {
        $division = $this->divisionRepository->findById($id);
        $parentDivisions = $this->divisionRepository->getAllOrderedByName();

        return view('pages.divisions.create', [
            'title' => 'Edit Divisi',
            'division' => $division,
            'parentDivisions' => $parentDivisions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:divisions,name,' . $id,
            'type' => 'required',
        ]);

        $result = $this->service->update($id, $request->all());

        if ($result['status']) {
            return redirect()->route('divisions.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    public function destroy($id)
    {
        $result = $this->service->delete($id);

        if (request()->ajax()) {
            return response()->json([
                'status' => $result,
                'message' => $result ? 'Divisi berhasil dihapus' : 'Gagal menghapus divisi',
            ]);
        }

        return redirect()->route('divisions.index')->with('success', $result ? 'Divisi berhasil dihapus' : 'Gagal menghapus divisi');
    }
}
