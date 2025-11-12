<?php

namespace App\Repositories;

use App\Interfaces\ReportJourneyRepositoryInterface;
use App\Models\ReportJourney;

class ReportJourneyRepository implements ReportJourneyRepositoryInterface
{
    public function store(array $data): ReportJourney
    {
        return ReportJourney::create($data);
    }
}
