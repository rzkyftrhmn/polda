<?php

namespace App\Http\Controllers;

use App\Enums\ReportJourneyType;
use App\Services\ReportJourneyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        try {
            $validated = $request->validate([
                'type' => [
                    'required',
                    Rule::in(array_map(static fn (ReportJourneyType $type) => $type->value, ReportJourneyType::manualOptions())),
                ],
                'description' => ['required', 'string'],
                'files' => ['nullable', 'array'],
                'files.*' => ['nullable', 'file', 'max:4096'],
                'institution_target_id' => [
                    'nullable',
                    'integer',
                    'exists:institutions,id',
                    'required_if:type,' . ReportJourneyType::TRANSFER->value,
                ],
                'division_target_id' => [
                    'nullable',
                    'integer',
                    'exists:divisions,id',
                    'required_if:type,' . ReportJourneyType::TRANSFER->value,
                ],
            ]);
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput()
                ->with('open_modal', 'journey');
        }

        $payload = [
            'report_id' => $reportId,
            'institution_id' => optional(auth()->user())->institution_id,
            'division_id' => optional(auth()->user())->division_id,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'institution_target_id' => $validated['institution_target_id'] ?? null,
            'division_target_id' => $validated['division_target_id'] ?? null,
        ];

        $files = $request->file('files', []);
        $files = is_array($files) ? $files : [$files];

        $result = $this->service->store($payload, array_filter($files));

        if ($result['status'] ?? false) {
            alert()->success($result['message']);

            return redirect()->route('reports.show', ['id' => $reportId]);
        }

        alert()->error($result['message'] ?? 'Gagal menambahkan tahapan penanganan.');

        return back()
            ->withInput()
            ->with('open_modal', 'journey');
    }
}

