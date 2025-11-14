<?php

namespace App\Http\Controllers;

use App\Enums\ReportJourneyType;
use App\Models\Division;
use App\Models\Institution;
use App\Models\Report;
use App\Services\ReportJourneyService;

class ReportController extends Controller
{
    public function __construct(
        protected ReportJourneyService $journeyService
    ) {
    }

    public function show($id)
    {
        $report = Report::with(['category', 'province', 'city', 'district'])->findOrFail($id);

        $journeys = $this->journeyService->paginateByReport($report->id, 5, order: 'desc');

        $institutions = Institution::orderBy('name')->get(['id', 'name']);
        $divisions = Division::with('parent')
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'parent_id']);

        $journeyTypes = ReportJourneyType::manualOptions();

        return view('pages.laporan.show', [
            'report' => $report,
            'journeys' => $journeys,
            'journeyTypes' => $journeyTypes,
            'institutions' => $institutions,
            'divisions' => $divisions,
            'statusLabel' => ReportJourneyType::tryFrom($report->status)?->label() ?? $report->status,
        ]);
    }

}
