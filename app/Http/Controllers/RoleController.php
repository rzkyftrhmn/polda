<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Repositories\PermissionRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $service, $fiture_title, $fiture_name, $fiture_path, $role, $user, $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository, RoleService $service)
    {
        $this->service = $service;
        $this->permissionRepository = $permissionRepository;
        $this->fiture_title = 'Role Management';
        $this->fiture_name = 'Role';
        $this->fiture_path = 'roles';

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        return view('pages.roles.index', [
            'title' => $this->fiture_title,
            'name' => $this->fiture_name,
            'path' => $this->fiture_path,
            'user' => $this->user,
        ]);
    }

    public function datatables(Request $request)
    {
        if ($request->ajax()) {
            $columns = [null, 'name', 'created_at', null];

            $limit = $request->input('length');
            $start = $request->input('start');
            $orderColIndex = $request->input('order.0.column');
            $dir = $request->input('order.0.dir', 'asc');
            $order = $columns[$orderColIndex] ?? 'created_at';
            $filter_q = $request->input('filter_q');

            $query = $this->service->getAllForDatatable(); // Role::with('permissions')->select(...)

            // Filter
            if (!empty($filter_q)) {
                $query->where('name', 'like', "%{$filter_q}%");
            }

            $search = $request->input('search.value');
            if (!empty($search)) {
                $query = $query->where('name', 'like', '%' . $search . '%');
            }

            $totalData = $query->count();

            // Order
            if ($order) {
                $query->orderBy($order, $dir);
            }

            // Pagination
            $roles = $query->skip($start)->take($limit)->get();

            $data = [];
            foreach ($roles as $key => $role) {
                $htmlButton = '<td class="text-nowrap">
                    <a href="' . route('roles.show', $role->id) . '" class="btn btn-info btn-sm content-icon btn-detail">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a href="' . route('roles.edit', $role->id) . '" class="btn btn-warning btn-sm content-icon btn-edit" data-id="' . $role->id . '">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="javascript:void(0);" 
                        class="btn btn-danger btn-sm content-icon btn-delete"
                        data-id="' . $role->id . '"
                        data-name="' . htmlspecialchars($role->name ?? '', ENT_QUOTES) . '"
                        data-url="' . route('roles.destroy', $role->id) . '"
                        data-title="Hapus Role?">
                        <i class="fa fa-times"></i>
                    </a>
                </td>';

                $data[] = [
                    'DT_RowIndex' => $key + 1 + $start,
                    'name' => $role->name,
                    'created_at' => $role->created_at->format('Y-m-d'),
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
        $permissions = $this->permissionRepository->getAllPermissions(); // dari repo kamu
        return view('pages.roles.form', [
            'title' => 'Tambah Role',
            'permissions' => $permissions,
            'role' => null,
            'defaultPermissions' => [
                'Admin' => $this->permissionRepository->AdminPermission(),
                'Polda' => $this->permissionRepository->PoldaPermission(),
                'Polres' => $this->permissionRepository->PolresPermission(),
                'Kasubbid' => $this->permissionRepository->KasubbidPermission(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('roles', 'name'),
            ],
            'permissions' => 'required|array|min:1',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan.',
            'permissions.required' => 'Minimal pilih satu permission.',
        ]);

        $result = $this->service->store($request->all());

        if ($result['status']) {
            return redirect()->route('roles.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    public function show($id)
    {
        $role = $this->service->getById($id);
        if (!$role) {
            return redirect()->route('roles.index')->with('error', 'Role tidak ditemukan.');
        }

        $permissions = $role->permissions->pluck('name')->toArray();

        return view('pages.roles.show', [
            'title' => $this->fiture_title,
            'name' => $this->fiture_name,
            'path' => $this->fiture_path,
            'user' => $this->user,
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = $this->permissionRepository->getAllPermissions();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('pages.roles.form', [
            'title' => 'Edit Role',
            'permissions' => $permissions,
            'role' => $role,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('roles', 'name')->ignore($id),
            ],
            'permissions' => 'required|array|min:1',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan.',
            'permissions.required' => 'Minimal pilih satu permission.',
        ]);

        $result = $this->service->update($id, $request->all());
        if ($result['status']) {
            return redirect()->route('roles.index')->with('success', $result['message']);
        }
        return back()->with('error', $result['message'])->withInput();
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (request()->ajax()) {
            return response()->json([
                'status' => $deleted,
                'message' => $deleted ? 'Role berhasil dihapus' : 'Gagal menghapus role'
            ]);
        }

        if ($deleted) {
            return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus');
        }
        return back()->with('error', 'Gagal menghapus role');
    }
}
