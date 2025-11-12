<?php

namespace App\Http\Controllers;

use App\Services\ReportJourneyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ReportJourneyController extends Controller
{
    public function __construct(
        protected ReportJourneyService $service
    ) {
    }

    public function store(Request $request, $reportId): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:PEMERIKSAAN,LIMPAH,SIDANG,SELESAI',
                'description' => 'required|string',
                'files.*' => 'nullable|file|max:4096',
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
        ];

        $files = $request->file('files') ?? [];

        if (! is_array($files)) {
            $files = [$files];
        }

        $files = array_filter($files, static fn ($file) => $file instanceof UploadedFile);

        $result = $this->service->store($payload, $files);

        if ($result['status'] ?? false) {
            return redirect()
                ->route('reports.show', ['id' => $reportId])
                ->with('success', $result['message'] ?? 'Tahapan penanganan berhasil ditambahkan.');
        }

        return back()
            ->with('error', $result['message'] ?? 'Gagal menambahkan tahapan penanganan.')
            ->withInput()
            ->with('open_modal', 'journey');
    }
}
