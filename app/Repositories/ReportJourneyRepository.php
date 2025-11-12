<?php

namespace App\Repositories;

use App\Interfaces\ReportJourneyRepositoryInterface;
use App\Models\ReportJourney;

class ReportJourneyRepository implements ReportJourneyRepositoryInterface
{
<<<<<<< HEAD
    public function store(array $data)
=======
    public function store(array $data): ReportJourney
>>>>>>> 02a3e64 (test: verify journey multi-upload success)
    {
        return ReportJourney::create($data);
    }
}
