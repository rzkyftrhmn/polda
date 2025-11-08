<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\InstitutionRepository;
use App\Services\InstitutionService;

class InstitutionController extends Controller
{
    protected $institutionRepository, $service, $user;

    public function __construct(InstitutionRepository $institutionRepository, InstitutionService $service)
    {
        $this->institutionRepository = $institutionRepository;
        $this->service = $service;

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
    }

    public function index()
    {
        return view('pages.institutions.index', [
            'title' => 'Institusi',
            'user' => $this->user,
            'types' => $this->institutionRepository->getDistinctTypes(),
        ]);
    }

    // public function datatables(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $columns = [
    //             null,
    //             'name',
    //             'type',
    //             'created_at',
    //             null,
    //         ];

    //         $limit = $request->input('length');
    //         $start = $request->input('start');
    //         $orderColIndex = $request->input('order.0.column');
    //         $dir = $request->input('order.0.dir', 'asc');
    //         $order = $columns[$orderColIndex] ?? 'created_at';
    //         $filter_q = $request->input('filter_q');
    //         $filter_type = $request->input('filter_type');

    //         $query = $this->institutionRepository->getAllForDatatable();

    //         if (!empty($filter_q)) {
    //             $query->where('name', 'like', "%{$filter_q}%");
    //         }

    //         if (!empty($filter_type)) {
    //             $query->where('type', $filter_type);
    //         }

    //         $search = $request->input('search.value');
    //         if (!empty($search)) {
    //             $query->where('name', 'like', "%{$search}%");
    //         }

    //         $totalData = $query->count();

    //         if ($order) {
    //             $query->orderBy($order, $dir);
    //         }

    //         $institutions = $query->skip($start)->take($limit)->get();

    //         $data = [];
    //         foreach ($institutions as $key => $institution) {
    //             $htmlButton = '<td class="text-nowrap">
    //                 <a href="' . route('institutions.edit', $institution->id) . '" class="btn btn-warning btn-sm content-icon">
    //                     <i class="fa fa-edit"></i>
    //                 </a>
    //                 <a href="javascript:void(0);" 
    //                     class="btn btn-danger btn-sm content-icon btn-delete"
    //                     data-id="' . $institution->id . '"
    //                     data-name="' . htmlspecialchars($institution->name, ENT_QUOTES) . '"
    //                     data-url="' . route('institutions.destroy', $institution->id) . '"
    //                     data-title="Hapus Institusi?">
    //                     <i class="fa fa-times"></i>
    //                 </a>
    //             </td>';

    //             $data[] = [
    //                 'DT_RowIndex' => $key + 1 + $start,
    //                 'name' => $institution->name,
    //                 'type' => $institution->type,
    //                 'created_at' => $institution->created_at ? $institution->created_at->format('d-m-Y') : '-',
    //                 'action' => $htmlButton,
    //             ];
    //         }

    //         return response()->json([
    //             'draw' => intval($request->input('draw')),
    //             'recordsTotal' => $totalData,
    //             'recordsFiltered' => $totalData,
    //             'data' => $data,
    //         ]);
    //     }
    // }

      public function datatables(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                null,
                'name',
                'type',
                'created_at',
                null,
            ];

            $limit = $request->input('length');
            $start = $request->input('start');
            $orderColIndex = $request->input('order.0.column');
            $dir = $request->input('order.0.dir', 'asc');
            $order = $columns[$orderColIndex] ?? 'created_at';
            $filter_q = $request->input('filter_q');
            $filter_type = $request->input('filter_type');

            $query = $this->institutionRepository->getAllForDatatable();

            if (!empty($filter_q)) {
                $query->where('name', 'like', "%{$filter_q}%");
            }

            if (!empty($filter_type)) {
                $query->where('type', $filter_type);
            }

            $search = $request->input('search.value');
            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%");
            }

            $totalData = $query->count();

            if ($order) {
                $query->orderBy($order, $dir);
            }

            $institutions = $query->skip($start)->take($limit)->get();

            $data = [];
            foreach ($institutions as $key => $institution) {
                $htmlButton = '<td class="text-nowrap">
                    <a href="' . route('institutions.edit', $institution->id) . '" class="btn btn-warning btn-sm content-icon">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="javascript:void(0);" 
                        class="btn btn-danger btn-sm content-icon btn-delete"
                        data-id="' . $institution->id . '"
                        data-name="' . htmlspecialchars($institution->name, ENT_QUOTES) . '"
                        data-url="' . route('institutions.destroy', $institution->id) . '"
                        data-title="Hapus Institusi?">
                        <i class="fa fa-times"></i>
                    </a>
                </td>';

                $data[] = [
                    'DT_RowIndex' => $key + 1 + $start,
                    'name' => $institution->name,
                    'type' => $institution->type,
                    'created_at' => $institution->created_at ? $institution->created_at->format('d-m-Y') : '-',
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
        return view('pages.institutions.create', [
            'title' => 'Tambah Institusi',
            'institution' => null,
            'types' => $this->institutionRepository->getDistinctTypes(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:institutions,name',
            'type' => 'required',
        ]);

        $result = $this->service->store($request->all());

        if ($result['status']) {
            return redirect()->route('institutions.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    public function edit($id)
    {
        $institution = $this->institutionRepository->findById($id);

        return view('pages.institutions.create', [
            'title' => 'Edit Institusi',
            'institution' => $institution,
            'types' => $this->institutionRepository->getDistinctTypes(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:institutions,name,' . $id,
            'type' => 'required',
        ]);

        $result = $this->service->update($id, $request->all());

        if ($result['status']) {
            return redirect()->route('institutions.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    public function destroy($id)
    {
        $result = $this->service->delete($id);

        if (request()->ajax()) {
            return response()->json([
                'status' => $result,
                'message' => $result ? 'Institusi berhasil dihapus' : 'Gagal menghapus institusi',
            ]);
        }

        return redirect()->route('institutions.index')->with('success', $result ? 'Institusi berhasil dihapus' : 'Gagal menghapus institusi');
    }
}
