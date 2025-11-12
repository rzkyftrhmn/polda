<?php

namespace App\Interfaces;

use App\Models\ReportFollowUp;

interface ReportFollowUpRepositoryInterface
{
    public function store(array $data): ReportFollowUp;
}
