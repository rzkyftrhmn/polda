<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\Suspect;
use App\Models\ReportJourney;
use Illuminate\Support\Facades\DB;

class PelaporanRepository
{
    public function getDataTableQuery($filter_q = null)
    {
        $query = Report::with(['suspects','reportCategory','province','city','district']);

        if ($filter_q) {
            $query->where('title','like',"%{$filter_q}%");
        }

        return $query;
    }

    public function store(array $data)
    {
        return DB::transaction(function() use ($data) {
            // Simpan report
            $report = Report::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'incident_datetime' => $data['incident_datetime'],
                'province_id' => $data['province_id'],
                'city_id' => $data['city_id'],
                'district_id' => $data['district_id'],
                'address_detail' => $data['address_detail'] ?? null,
                'category_id' => $data['category_id'],
                'status' => $data['status'],
                'code' => $data['code'], 
                'finish_time' => $data['finish_time'] ?? null,
            ]);

            if(!empty($data['suspects'])) {
                foreach($data['suspects'] as $suspect){
                    Suspect::create([
                        'report_id' => $report->id,
                        'name' => $suspect['name'],
                        'description' => $suspect['description'] ?? null,
                    ]);
                }
            }

            ReportJourney::create([
                'report_id' => $report->id,
                'institution_id' => null,
                'division_id' => null,
                'type' => 'SUBMITTED',
                'description' => 'Report submitted',
            ]);

            return $report;
        });
    }

    public function getById($id)
    {
        return Report::with(['suspects','reportCategory','province','city','district'])->find($id);
    }

    public function update($id, array $data)
    {
        $report = Report::find($id);
        if(!$report) return null;

        DB::transaction(function() use ($report, $data) {
            $report->update($data);

            if(isset($data['suspects'])){
                $report->suspects()->delete();
                foreach($data['suspects'] as $suspect){
                    Suspect::create([
                        'report_id' => $report->id,
                        'name' => $suspect['name'],
                        'description' => $suspect['description'] ?? null,
                    ]);
                }
            }
        });

        return $report;
    }

    public function delete($id)
    {
        $report = Report::find($id);
        if(!$report) return false;

        return DB::transaction(function() use ($report) {
            $report->suspects()->delete();
            $report->journeys()->delete();
            return $report->delete();
        });
    }
}
