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
        $report = Report::with('category')->findOrFail($id);

        $journeys = $this->journeyService->paginateByReport($report->id, 5, order: 'desc');

        $institutions = Institution::orderBy('name')->get(['id', 'name']);
        $divisions = Division::orderBy('name')->get(['id', 'name', 'institution_id']);

        $journeyTypes = ReportJourneyType::manualOptions();

        return view('pages.reports.detail', [
            'report' => $report,
            'journeys' => $journeys,
            'journeyTypes' => $journeyTypes,
            'institutions' => $institutions,
            'divisions' => $divisions,
            'statusLabel' => ReportJourneyType::tryFrom($report->status)?->label() ?? $report->status,
        ]);
    }

}
