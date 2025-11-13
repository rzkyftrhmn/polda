<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PermissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    protected $service, $fiture_title, $fiture_name, $fiture_path, $user;

    public function __construct(PermissionService $service)
    {
        $this->service = $service;
        $this->fiture_title = 'Permission Management';
        $this->fiture_name = 'Permission';
        $this->fiture_path = 'permissions';

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        return view('pages.permissions.index', [
            'title' => $this->fiture_title,
            'name' => $this->fiture_name,
            'path' => $this->fiture_path,
            'user' => $this->user,
        ]);
    }

    public function datatables(Request $request)
    {
        if ($request->ajax()) {
            $columns = [
                null,
                'name',
                'guard_name',
                null,
            ];

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $query = $this->service->getAllForDatatable();

            $filter_q = $request->input('filter_q');
            if (!empty($filter_q)) {
                $query->where('name', 'like', "%{$filter_q}%");
            }

            // search bawaan datatables (biar kompatibel)
            $search = $request->input('search.value');
            if (!empty($search)) {
                $query = $query->where('name', 'like', '%' . $search . '%');
            }

            $totalData = $query->count();
            if ($order) {
                $query = $query->orderBy($order, $dir);
            }

            $permissions = $query->skip($start)->take($limit)->get();
            $data = [];
            foreach ($permissions as $key => $perm) {
                $action = '
                    <a href="' . route('permissions.edit', $perm->id) . '" class="btn btn-warning btn-sm content-icon">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="javascript:void(0);" 
                        class="btn btn-danger btn-sm content-icon btn-delete"
                        data-id="' . $perm->id . '"
                        data-name="' . htmlspecialchars($perm->name ?? '', ENT_QUOTES) . '"
                        data-url="' . route('permissions.destroy', $perm->id) . '"
                        data-title="Hapus Permission?">
                        <i class="fa fa-times"></i>
                    </a>
                ';

                $data[] = [
                    'DT_RowIndex' => ($key + 1) + $start,
                    'name' => $perm->name,
                    'guard_name' => $perm->guard_name,
                    'action' => $action,
                ];
            }

            $json_data = [
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalData),
                "data" => $data,
            ];

            return response()->json($json_data);
        }
    }


    public function create()
    {
        return view('pages.permissions.create', [
            'title' => $this->fiture_title,
            'name' => $this->fiture_name,
            'path' => $this->fiture_path,
            'user' => $this->user,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'unique:permissions,name'],
        ]);

        DB::beginTransaction();
        try {
            $result = $this->service->store($validated);

            if ($result) {
                DB::commit();
                return redirect()->route('permissions.index')->with('success', 'Permission berhasil dibuat.');
            }

            DB::rollBack();
            return back()->with('error', 'Gagal membuat permission.');
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('Permission store failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat membuat permission.');
        }
    }

    public function edit($id)
    {
        $permission = $this->service->getById($id);
        if (!$permission) {
            return redirect()->route('permissions.index')->with('error', 'Permission tidak ditemukan.');
        }

        return view('pages.permissions.create', [
            'title' => $this->fiture_title,
            'name' => $this->fiture_name,
            'path' => $this->fiture_path,
            'user' => $this->user,
            'editPermission' => $permission,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', Rule::unique('permissions', 'name')->ignore($id)],
        ]);

        DB::beginTransaction();
        try {
            $result = $this->service->update($id, $validated);

            if ($result) {
                DB::commit();
                return redirect()->route('permissions.index')->with('success', 'Permission berhasil diperbarui.');
            }

            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui permission.');
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('Permission update failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan saat memperbarui permission.');
        }
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (request()->ajax()) {
            return response()->json([
                'status' => $deleted,
                'message' => $deleted ? 'Permission berhasil dihapus' : 'Gagal menghapus permission'
            ]);
        }

        if ($deleted) {
            return redirect()->route('permissions.index')->with('success', 'Permission berhasil dihapus');
        }
        return back()->with('error', 'Gagal menghapus permission');
    }
}
