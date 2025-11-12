<?php

namespace App\Repositories;

use App\Interfaces\ReportJourneyRepositoryInterface;
use App\Models\ReportJourney;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReportJourneyRepository implements ReportJourneyRepositoryInterface
{
    public function store(array $data): ReportJourney
    {
        return ReportJourney::create($data);
    }

    public function paginateByReport(int $reportId, int $perPage = 5): LengthAwarePaginator
    {
        return ReportJourney::with('evidences')
            ->where('report_id', $reportId)
            ->orderBy('created_at')
            ->paginate($perPage);
    }
}
