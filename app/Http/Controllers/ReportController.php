<?php

namespace App\Http\Controllers;

use App\Models\Report;

class ReportController extends Controller
{
    public function show($id)
    {
        $report = Report::with('category')->findOrFail($id);

        $journeys = $report->journeys()
            ->with('evidences')
            ->orderByDesc('created_at')
            ->paginate(5);

        $followUps = $report->followUps()
            ->with('user')
            ->latest()
            ->paginate(5);

        return view('pages.reports.detail', compact('report', 'journeys', 'followUps'));
    }
}
