<?php

namespace App\Interfaces;

use App\Models\ReportJourney;

interface ReportJourneyRepositoryInterface
{
    public function store(array $data): ReportJourney;
}
