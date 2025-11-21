<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\Suspect;
use App\Models\ReportJourney;
use App\Models\AccessData;
use App\Models\User;
use App\Models\InstructionsAndDirection;
use Illuminate\Support\Collection;

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
        // dd('MAMPIR KE createAccess', $data);
        return AccessData::create($data);
    }


    public function find($id)
    {
        return Report::with([
            'suspects', 
            'suspects.division',   
            'journeys', 
            'accessDatas',
            'province', 
            'city', 
            'district'
        ])->find($id);
    }


    public function updateReport($report, array $data)
    {
        return $report->update($data);
    }


    public function getUsersByDivisionIds(array $divisionIds): Collection
    {
        return empty($divisionIds)
            ? collect()
            : User::whereIn('division_id', $divisionIds)->get();
    }

    public function getAdminUsers(): Collection
    {
        return User::role([ROLE_ADMIN])->get();
    }

    public function getInstructionsByReportId(int $reportId): Collection
    {
        return InstructionsAndDirection::where('report_id', $reportId)
            ->orderByDesc('id')
            ->get();
    }

    public function getUsersByIds(array $ids): Collection
    {
        return empty($ids)
            ? collect()
            : User::whereIn('id', $ids)->get();
    }

    public function createInstruction(array $data)
    {
        return InstructionsAndDirection::create($data);
    }

}
