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

    public function paginateByReport(int $reportId, int $perPage = 5, string $order = 'desc'): LengthAwarePaginator
    {
        $direction = strtolower($order) === 'asc' ? 'asc' : 'desc';

        return ReportJourney::where('report_id', $reportId)
            ->with(['division', 'evidences'])
            ->orderBy('id', $order)
            ->paginate($perPage);
    }
}
