<?php

namespace App\Interfaces;

use App\Models\ReportJourney;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReportJourneyRepositoryInterface
{
    public function store(array $data): ReportJourney;

    public function paginateByReport(int $reportId, int $perPage = 5, string $order = 'desc'): LengthAwarePaginator;
}
