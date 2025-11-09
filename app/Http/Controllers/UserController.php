<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $service, $fiture_title, $fiture_name, $fiture_path, $user;
    public function __construct(
        UserService $service,
    ) {
        $this->service = $service;
        $this->fiture_title = 'User Management';
        $this->fiture_name = 'User';
        $this->fiture_path = 'user';

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.users.index', [
            'title' => $this->fiture_title,
            'name' => $this->fiture_name,
            'path' => $this->fiture_path,
            'user' => $this->user,
        ]);
    }

    public function datatables(Request $request)
    {
        if (request()->ajax()) {
            /**
             * Columns used for ordering (aligned with DataTables columns order on the client)
             * 0: DT_RowIndex (not orderable)
             * 1: institutions.name
             * 2: divisions.name
             * 3: users.name
             * 4: users.email
             * 5: action (not orderable)
             */
            $columns = [
                null,
                'institutions.name',
                'divisions.name',
                'users.name',
                'users.email',
                null,
            ];

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            // Move join/select logic into repository per Service-Repository pattern
            $posts = $this->service->getAllForDatatable();
            // Extra filters from custom form
            $filterQ = $request->input('filter_q');
            if (!empty($filterQ)) {
                $posts = $posts->where(function ($q) use ($filterQ) {
                    $q->where('users.username', 'like', '%' . $filterQ . '%')
                        ->orWhere('users.name', 'like', '%' . $filterQ . '%')
                        ->orWhere('users.email', 'like', '%' . $filterQ . '%');
                });
            }
            $filterInstitutionId = $request->input('filter_institution_id');
            if (!empty($filterInstitutionId)) {
                $posts = $posts->where('institution_id', $filterInstitutionId);
            }
            $filterDivisionId = $request->input('filter_division_id');
            if (!empty($filterDivisionId)) {
                $posts = $posts->where('division_id', $filterDivisionId);
            }
            // Global search
            $globalSearch = $request->input('search.value');
            if (!empty($globalSearch)) {
                $posts = $posts->where(function ($q) use ($globalSearch) {
                    $q->where('users.username', 'like', '%' . $globalSearch . '%')
                        ->orWhere('users.name', 'like', '%' . $globalSearch . '%')
                        ->orWhere('users.email', 'like', '%' . $globalSearch . '%')
                        ->orWhere('institutions.name', 'like', '%' . $globalSearch . '%')
                        ->orWhere('divisions.name', 'like', '%' . $globalSearch . '%');
                });
            }

            // Per-column filtering (optional)
            foreach ((array) $request->columns as $key => $col) {
                $search = $request->input('columns.' . $key . '.search.value');
                $columnName = $col['name'] ?? null; // use actual DB column name from client
                if ($columnName && !is_null($search) && $search !== '') {
                    $allowed = [
                        'users.username',
                        'users.name',
                        'users.email',
                        'institutions.name',
                        'divisions.name',
                    ];
                    if (in_array($columnName, $allowed)) {
                        $posts = $posts->where($columnName, 'like', '%' . $search . '%');
                    }
                }
            }

            $totalData = $posts->count();
            // Apply ordering only if the target column is orderable
            if ($order) {
                $posts = $posts->orderBy($order, $dir);
            }
            $posts = $posts->skip($start)->take($limit)->get();
            $data = array();
            if (!empty($posts)) {
                foreach ($posts as $key => $post) {
                    // Action buttons: detail, edit, delete (SweetAlert confirmation)
                    $htmlButton = '<td class="text-nowrap"> 
                        <a href="' . route('users.show', $post->id) . '" 
                            class="btn btn-info btn-sm content-icon btn-detail"> 
                            <i class="fa fa-eye"></i> 
                        </a> 
                        <a href="' . route('users.edit', $post->id) . '" 
                            class="btn btn-warning btn-sm content-icon btn-edit" data-id="' . $post->id . '"> 
                            <i class="fa fa-edit"></i> 
                        </a> 
                        <a href="javascript:void(0);" 
                            class="btn btn-danger btn-sm content-icon btn-delete"
                            data-id="' . $post->id . '"
                            data-name="' . htmlspecialchars($post->name ?? '', ENT_QUOTES) . '"
                            data-url="' . route('users.destroy', $post->id) . '"
                            data-title="Hapus User?">
                            <i class="fa fa-times"></i>
                        </a>
                    </td>';

                    $nestedData['username'] = $post->username ?? '-';
                    $nestedData['name'] = $post->name ?? '-';
                    $nestedData['email'] = $post->email ?? '-';
                    $nestedData['institution_name'] = $post->institution_name ?? '-';
                    $nestedData['division_name'] = $post->division_name ?? '-';
                    $nestedData['created_at'] = backChangeFormatDate($post->created_at) ?? '-';
                    $nestedData['action'] = $htmlButton;
                    $nestedData['DT_RowIndex'] = ($key + 1) + $start;
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalData),
                "data"            => $data
            );

            return response()->json($json_data);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch master data via repository-service pattern
        $institutions = $this->service->getInstitutions();
        $divisions = $this->service->getDivisions();
        $roles = $this->service->getRoles();

        return view('pages.users.create', [
            'title' => $this->fiture_title,
            'name' => $this->fiture_name,
            'path' => $this->fiture_path,
            'user' => $this->user,
            'institutions' => $institutions,
            'divisions' => $divisions,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6','confirmed'],
            'username' => ['nullable','string','max:255','unique:users,username'],
            'institution_id' => ['nullable','integer','exists:institutions,id'],
            'division_id' => ['nullable','integer','exists:divisions,id'],
            'role' => ['required','string','exists:roles,name'],
        ]);

        DB::beginTransaction();
        try {
            // Store via service-repository pattern
            $result = $this->service->store($validated);

            if (!empty($result['status'])) {
                DB::commit();
                return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
            }

            DB::rollBack();
            return back()->withInput()->with('error', $result['message'] ?? 'Terjadi kesalahan saat membuat user.');
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('User store failed', [
                'message' => $e->getMessage(),
            ]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat user.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = $this->service->getById($id);
        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User tidak ditemukan.');
        }
        // Load relations for display
        $user->load('institution','division','roles');
        $roleNames = $user->getRoleNames();

        return view('pages.users.show', [
            'title' => $this->fiture_title,
            'name' => $this->fiture_name,
            'path' => $this->fiture_path,
            'user' => $this->user,
            'detailUser' => $user,
            'roleNames' => $roleNames,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = $this->service->getById($id);
        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User tidak ditemukan.');
        }

        // Master data
        $institutions = $this->service->getInstitutions();
        $divisions = $this->service->getDivisions();
        $roles = $this->service->getRoles();
        $currentRole = $user->roles()->pluck('name')->first();

        // Reuse the same blade as create
        return view('pages.users.create', [
            'title' => $this->fiture_title,
            'name' => $this->fiture_name,
            'path' => $this->fiture_path,
            'user' => $this->user,
            'institutions' => $institutions,
            'divisions' => $divisions,
            'roles' => $roles,
            'editUser' => $user,
            'currentRole' => $currentRole,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($id)],
            'password' => ['nullable','string','min:6','confirmed'],
            'username' => ['nullable','string','max:255', Rule::unique('users','username')->ignore($id)],
            'institution_id' => ['nullable','integer','exists:institutions,id'],
            'division_id' => ['nullable','integer','exists:divisions,id'],
            'role' => ['required','string','exists:roles,name'],
        ]);

        // Do not overwrite password if left blank
        if (empty($validated['password'] ?? null)) {
            unset($validated['password'], $validated['password_confirmation']);
        }

        DB::beginTransaction();
        try {
            $result = $this->service->update($id, $validated);

            if (!empty($result['status'])) {
                DB::commit();
                return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
            }

            DB::rollBack();
            return back()->withInput()->with('error', $result['message'] ?? 'Terjadi kesalahan saat memperbarui user.');
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('User update failed', [
                'message' => $e->getMessage(),
            ]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui user.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            // Prevent deleting currently authenticated user
            if (($this->user && (int)$this->user->id === (int)$id)) {
                $message = 'Anda tidak dapat menghapus akun Anda sendiri.';
                if (request()->ajax()) {
                    return response()->json(['status' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            $deleted = $this->service->delete($id);
            if ($deleted) {
                DB::commit();
                if (request()->ajax()) {
                    return response()->json(['status' => true, 'message' => 'User berhasil dihapus.']);
                }
                return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
            }

            DB::rollBack();
            $message = 'Tidak dapat menghapus user.';
            if (request()->ajax()) {
                return response()->json(['status' => false, 'message' => $message], 400);
            }
            return back()->with('error', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('User delete failed', [ 'message' => $e->getMessage() ]);
            $message = 'Terjadi kesalahan saat menghapus user.';
            if (request()->ajax()) {
                return response()->json(['status' => false, 'message' => $message], 500);
            }
            return back()->with('error', $message);
        }
    }
}
