<?php

namespace App\Http\Controllers;

use App\Services\InstitutionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InstitutionController extends Controller
{
    protected $service;

    public function __construct(InstitutionService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    public function index()
    {
        $types = $this->service->getTypes();
        return view('pages.institutions.index', compact('types'));
    }

    public function datatables(Request $request)
    {
        $query = $this->service->getAllForDatatable();
        $filterQ = $request->input('filter_q');
        $filterType = $request->input('filter_type');

        if ($filterQ) {
            $query->where('name', 'like', "%$filterQ%");
        }
        if ($filterType) {
            $query->where('type', $filterType);
        }

        $data = $query->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $data]);
    }

    public function create()
    {
        $types = $this->service->getTypes();
        return view('pages.institutions.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:institutions,name',
            'type' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $result = $this->service->store($validated);
            DB::commit();
            return redirect()->route('institutions.index')->with('success', $result['message']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $institution = $this->service->getById($id);
        $types = $this->service->getTypes();
        return view('pages.institutions.create', compact('institution', 'types'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('institutions', 'name')->ignore($id)],
            'type' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $result = $this->service->update($id, $validated);
            DB::commit();
            return redirect()->route('institutions.index')->with('success', $result['message']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->service->delete($id);
            return response()->json(['status' => true, 'message' => $result['message']]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
