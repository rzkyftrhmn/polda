<?php

namespace App\Repositories;

use App\Interfaces\ReportFollowUpRepositoryInterface;
use App\Models\ReportFollowUp;

class ReportFollowUpRepository implements ReportFollowUpRepositoryInterface
{
    public function store(array $data): ReportFollowUp
    {
        return ReportFollowUp::create($data);
    }
}
