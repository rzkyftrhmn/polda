<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\Suspect;
use App\Models\ReportJourney;
use App\Models\AccessData;

class PelaporanRepository
{
    public function createReport(array $data)
    {
        return Report::create($data);
    }

    public function createSuspect(array $data)
    {
        return Suspect::create($data);
    }

    public function createJourney(array $data)
    {
        return ReportJourney::create($data);
    }

    public function createAccess(array $data)
    {
        return AccessData::create($data);
    }

    public function find($id)
    {
        return Report::with(['suspects', 'journeys', 'accessDatas', 'province', 'city', 'district'])
            ->find($id);
    }

}
