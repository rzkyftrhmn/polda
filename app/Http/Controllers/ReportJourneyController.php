<?php

namespace App\Http\Controllers;

use App\Enums\ReportJourneyType;
use App\Services\ReportJourneyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReportJourneyController extends Controller
{
    public function __construct(
        protected ReportJourneyService $service
    ) {
    }

    public function store(Request $request, int $reportId): RedirectResponse
    {
        $limpahValues = [
            ReportJourneyType::TRANSFER->value,
            ReportJourneyType::TRANSFER->name,
            ReportJourneyType::TRANSFER->label(),
            'TRANSFER',
        ];

        try {
            $validated = $request->validate([
                'type' => [
                    'required',
                    Rule::in(array_map(static fn (ReportJourneyType $type) => $type->value, ReportJourneyType::manualOptions())),
                ],
                'description' => ['required', 'string'],
                'files' => ['nullable', 'array'],
                'files.*' => ['nullable', 'file', 'max:4096', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
                'institution_target_id' => [
                    'nullable',
                    'integer',
                    'exists:institutions,id',
                    Rule::requiredIf(fn () => in_array($request->input('type'), $limpahValues, true)),
                ],
                'subdivision_target_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('divisions', 'id')->where(static function ($query) {
                        $query->whereNotNull('parent_id');
                    }),
                    Rule::requiredIf(fn () => in_array($request->input('type'), $limpahValues, true)),
                ],
            ]);
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput()
                ->with('open_modal', 'journey');
        }

        if (! in_array($validated['type'], $limpahValues, true)) {
            $validated['institution_target_id'] = null;
            $validated['subdivision_target_id'] = null;
        }

        $payload = [
            'report_id' => $reportId,
            'institution_id' => optional(auth()->user())->institution_id,
            'division_id' => optional(auth()->user())->division_id,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'institution_target_id' => $validated['institution_target_id'] ?? null,
            'division_target_id' => $validated['subdivision_target_id'] ?? null,
        ];

        $files = $request->file('files', []);
        $files = is_array($files) ? $files : [$files];
        $files = array_filter($files, static fn ($file) => $file instanceof UploadedFile);

        $result = $this->service->store($payload, $files);

        if ($result['status'] ?? false) {
            return redirect()
                ->route('reports.show', ['id' => $reportId])
                ->with('success', $result['message']);
        }

        return back()
            ->withInput()
            ->with('open_modal', 'journey')
            ->with('error', $result['message'] ?? 'Gagal menambahkan tahapan penanganan.');
    }
}

