<?php

namespace App\Http\Controllers;

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
        $action = $request->input('action');

        $validated = $request->validate([
            'action' => ['required', Rule::in(['complete', 'transfer'])],
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
                'nullable',
                'integer',
                'exists:institutions,id',
                Rule::requiredIf(fn () => $action === 'transfer'),
            ],
            'target_division_id' => [
                'nullable',
                'integer',
                'exists:divisions,id',
                Rule::requiredIf(fn () => $action === 'transfer'),
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

        $user = Auth::user();

        $result = $this->service->storeProgress(
            $report,
            $validated,
            $files,
            $user?->division_id,
            $user?->institution_id,
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

