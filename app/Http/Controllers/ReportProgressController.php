<?php

namespace App\Http\Controllers;

use App\Enums\ReportJourneyType;
use App\Models\Division;
use App\Models\Report;
use App\Services\ReportJourneyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReportProgressController extends Controller
{
    public function __construct(protected ReportJourneyService $service)
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Report $report): RedirectResponse
    {
        $user = Auth::user();
        $division = $user?->division;

        $action = $request->input('action');
        $flow = $request->input('flow', 'inspection');

        $validated = $request->validate([
            'action' => ['required', Rule::in(['save', 'complete', 'transfer'])],
            'flow' => ['required', Rule::in(['inspection', 'investigation'])],

            'inspection_doc_number' => ['nullable', 'string'],
            'inspection_doc_date' => ['nullable', 'date'],
            'inspection_conclusion' => ['nullable', 'string'],
            'inspection_files' => ['nullable', 'array'],
            'inspection_files.*' => ['nullable', 'file', 'max:4096', 'mimes:jpg,jpeg,png,pdf,doc,docx'],

            'admin_documents' => ['nullable', 'array'],
            'admin_documents.*.name' => ['required_with:admin_documents', 'string'],
            'admin_documents.*.number' => ['nullable', 'string'],
            'admin_documents.*.date' => ['nullable', 'date'],
            'admin_documents.*.file' => ['nullable', 'file', 'max:4096', 'mimes:jpg,jpeg,png,pdf,doc,docx'],

            'trial_doc_number' => ['nullable', 'string'],
            'trial_doc_date' => ['nullable', 'date'],
            'trial_file' => ['nullable', 'file', 'max:4096', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
            'trial_decision' => ['nullable', 'string'],

            'target_institution_id' => [
                Rule::requiredIf(fn () => $action === 'transfer' && $flow === 'inspection'),
                'nullable',
                'integer',
                'exists:institutions,id',
            ],
            'target_division_id' => [
                Rule::requiredIf(fn () => $action === 'transfer' && $flow === 'inspection'),
                'nullable',
                'integer',
                'exists:divisions,id',
            ],
        ]);

        $inspectionFiles = $request->file('inspection_files', []);
        $inspectionFiles = is_array($inspectionFiles) ? $inspectionFiles : [$inspectionFiles];

        $adminFiles = [];
        foreach ($request->file('admin_documents', []) as $idx => $docFiles) {
            $adminFiles[$idx] = $docFiles['file'] ?? null;
        }

        $files = [
            'inspection_files' => array_filter($inspectionFiles),
            'admin_files' => $adminFiles,
            'trial_file' => $request->file('trial_file'),
        ];

        $divisionId = $division?->id;
        $institutionId = $user?->institution_id;

        if ($flow === 'inspection' && !$division?->canInspection()) {
            return back()->with('error', 'Divisi Anda tidak dapat melakukan pemeriksaan.');
        }

        if ($flow === 'investigation' && !$division?->canInvestigation()) {
            return back()->with('error', 'Divisi Anda tidak dapat melakukan penyidikan.');
        }

        if ($action === 'transfer' && $flow === 'inspection') {
            $targetDivision = Division::find($validated['target_division_id']);
            if ($targetDivision && !$targetDivision->canInvestigation()) {
                return back()->with('error', 'Unit tujuan tidak memiliki kewenangan penyidikan.');
            }
        }

        $result = $this->service->storeProgress(
            $report,
            $validated,
            $files,
            $divisionId,
            $institutionId,
            $user?->id,
        );

        if ($result['status']) {
            return redirect()
                ->route('pelaporan.show', ['pelaporan' => $report->id])
                ->with('success', $result['message']);
        }

        return back()
            ->withInput()
            ->with('error', $result['message'] ?? 'Gagal memperbarui progress laporan.');
    }
}

