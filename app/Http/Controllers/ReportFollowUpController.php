<?php

namespace App\Http\Controllers;

use App\Services\ReportFollowUpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportFollowUpController extends Controller
{
    public function __construct(
        protected ReportFollowUpService $service
    ) {
    }

    public function store(Request $request, $reportId): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'notes' => 'required|string',
            ]);
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput()
                ->with('open_modal', 'followup');
        }

        $payload = [
            'report_id' => $reportId,
            'user_id' => optional(auth()->user())->id,
            'notes' => $validated['notes'],
        ];

        $result = $this->service->store($payload);

        if ($result['status'] ?? false) {
            alert()->success($result['message'] ?? 'Catatan tindak lanjut berhasil ditambahkan.');

            return redirect()->route('reports.show', ['id' => $reportId]);
        }

        alert()->error($result['message'] ?? 'Gagal menambahkan catatan tindak lanjut.');

        return back()
            ->withInput()
            ->with('open_modal', 'followup');
    }
}
