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
use Illuminate\Support\Facades\Validator;

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

        $this->service->ensureInitialAccess($report);

        $action = $request->input('action');
        $flow = $request->input('flow', 'inspection');

        $isAdmin = $user && method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole(['super admin', 'super-admin', 'admin'])
            : false;

        $hasAccess = $this->service->hasAccess($division, $report, $isAdmin);

        if ($division?->canInspection() && !$division?->canInvestigation()) {
            $flow = 'inspection';
        } elseif ($division?->canInvestigation() && !$division?->canInspection()) {
            $flow = 'investigation';
        }

        $request->merge(['flow' => $flow]);

        if (!$hasAccess) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengupdate progress laporan ini.');
        }

        if ($flow === 'inspection' && !$isAdmin && !$division?->canInspection()) {
            return back()->with('error', 'Divisi Anda tidak dapat melakukan pemeriksaan.');
        }

        if ($flow === 'investigation' && !$isAdmin && !$division?->canInvestigation()) {
            return back()->with('error', 'Divisi Anda tidak dapat melakukan penyidikan.');
        }

        $validator = Validator::make($request->all(), [
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

        $validator->after(function ($validator) use ($request, $action, $flow) {
            if ($flow === 'inspection' && in_array($action, ['save', 'complete', 'transfer'], true)) {
                if (!$request->filled('inspection_doc_number')) {
                    $validator->errors()->add('inspection_doc_number', 'Nomor dokumen pemeriksaan wajib diisi.');
                }
                if (!$request->filled('inspection_doc_date')) {
                    $validator->errors()->add('inspection_doc_date', 'Tanggal dokumen pemeriksaan wajib diisi.');
                }
                if (!$request->filled('inspection_conclusion')) {
                    $validator->errors()->add('inspection_conclusion', 'Kesimpulan wajib diisi.');
                }
                $inspectionFiles = $request->file('inspection_files', []);
                $hasInspectionFile = false;
                foreach ((array) $inspectionFiles as $file) {
                    if ($file) {
                        $hasInspectionFile = true;
                        break;
                    }
                }
                if (!$hasInspectionFile) {
                    $validator->errors()->add('inspection_files', 'Minimal satu file pemeriksaan harus diunggah.');
                }
            }

            foreach ($request->input('admin_documents', []) as $idx => $doc) {
                $file = $request->file("admin_documents.$idx.file");
                if (!($doc['name'] ?? null)) {
                    $validator->errors()->add("admin_documents.$idx.name", 'Nama dokumen administrasi wajib diisi.');
                }
                if (!$file) {
                    $validator->errors()->add("admin_documents.$idx.file", 'File dokumen administrasi wajib diunggah.');
                }
                if (!($doc['number'] ?? null)) {
                    $validator->errors()->add("admin_documents.$idx.number", 'Nomor dokumen administrasi wajib diisi.');
                }
                if (!($doc['date'] ?? null)) {
                    $validator->errors()->add("admin_documents.$idx.date", 'Tanggal dokumen administrasi wajib diisi.');
                }
            }

            $requiresTrial = $flow === 'investigation' && (
                $action === 'complete'
                || $request->filled('trial_doc_number')
                || $request->filled('trial_doc_date')
                || $request->file('trial_file')
                || $request->filled('trial_decision')
            );

            if ($requiresTrial) {
                foreach ([
                    'trial_doc_number' => 'Nomor dokumen sidang wajib diisi.',
                    'trial_doc_date' => 'Tanggal dokumen sidang wajib diisi.',
                    'trial_decision' => 'Putusan sidang wajib diisi.',
                ] as $field => $message) {
                    if (!$request->filled($field)) {
                        $validator->errors()->add($field, $message);
                    }
                }
                if (!$request->file('trial_file')) {
                    $validator->errors()->add('trial_file', 'File dokumen sidang wajib diunggah.');
                }
            }
        });

        $validator->validate();
        $validated = $validator->validated();

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

        if ($flow === 'investigation' && $action === 'transfer') {
            return back()->with('error', 'Tahapan penyidikan tidak mendukung limpah.');
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
                ->route('pelaporan.show', $report->id)
                ->with('success', $result['message']);
        }

        return back()
            ->withInput()
            ->with('error', $result['message'] ?? 'Gagal memperbarui progress laporan.');
    }
}

