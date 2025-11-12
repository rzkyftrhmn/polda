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

        $journeys = $this->journeyService->paginateByReport($report->id, 5);

        $followUps = $report->followUps()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(5);

        $institutions = Institution::orderBy('name')->get(['id', 'name']);
        $divisions = Division::orderBy('name')->get(['id', 'name', 'institution_id']);

        $journeyTypes = ReportJourneyType::manualOptions();

        return view('pages.reports.detail', [
            'report' => $report,
            'journeys' => $journeys,
            'followUps' => $followUps,
            'journeyTypes' => $journeyTypes,
            'institutions' => $institutions,
            'divisions' => $divisions,
        ]);
    }

}
