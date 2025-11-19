<?php

namespace App\Services;

use App\Enums\ReportJourneyType;
use App\Repositories\PelaporanRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Report;

class PelaporanService
{
    protected $repo;

    public function __construct(PelaporanRepository $repo)
    {
        $this->repo = $repo;
    }
    
    public function store(array $data){

        DB::beginTransaction();

        $user = auth()->user();
        $divisionId = $user?->division_id;

        if (!$divisionId) {
            throw new \Exception('Division id tidak ditentukan.');
        }

        $data['province_id'] = 12;

        $report = $this->repo->createReport([
            'title' => $data['title'],
            'incident_datetime' => $data['incident_datetime'],
            'category_id' => $data['category_id'],
            'description' => $data['description'],
            'province_id' => 12,
            'city_id' => $data['city_id'],
            'district_id' => $data['district_id'],
            'address_detail' => $data['address_detail'],
            'name_of_reporter' => $data['name_of_reporter'],
            'address_of_reporter' => $data['address_of_reporter'],
            'phone_of_reporter' => $data['phone_of_reporter'],
            'created_by' => $user?->id,
            'status' => ReportJourneyType::SUBMITTED->value,
            'division_id' => $divisionId,
            'code' => $this->generateReportCode(),
        ]);

        $this->repo->createJourney([
            'report_id' => $report->id,
            'division_id' => $divisionId,
            'type' => 'SUBMITTED',
            'description' => $data['description'],
        ]);

        if (!empty($data['suspects'])) {
            foreach ($data['suspects'] as $suspect) {

                $suspectDivision = $suspect['satker_id']
                    ?? $suspect['satwil_id']
                    ?? $divisionId;

                $this->repo->createSuspect([
                    'report_id'   => $report->id,
                    'name'        => $suspect['name'],
                    'division_id' => $suspectDivision,
                ]);
            }
        }

        $this->repo->createAccess([
            'report_id'  => $report->id,
            'division_id'=> $divisionId,
            'is_finish'  => false,
        ]);

        DB::commit();

        return $report;
    }


    private function generateReportCode()
    {
        $dateCode = now()->format('md'); 
        $prefix = "#RPT-{$dateCode}";

        $last = Report::where('code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$last) {
            $number = 1;
        } else {
            $lastNumber = (int) substr($last->code, -3);
            $number = $lastNumber + 1;
        }

        $running = str_pad($number, 3, '0', STR_PAD_LEFT);

        return "{$prefix}{$running}";
    }

    public function datatables($filter_q = null)
    {
        $query = Report::query()
            ->with(['province', 'city', 'district']); 

        // Jika ada filter tambahan
        if (!empty($filter_q)) {
            $query->where(function($q) use ($filter_q) {
                $q->where('title', 'like', "%$filter_q%")
                ->orWhere('status', 'like', "%$filter_q%");
            });
        }

        return $query;
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $report = $this->repo->find($id);

            if (!$report) {
                throw new \Exception('Laporan tidak ditemukan.');
            }

            if ($report->suspects()->exists()) {
                $report->suspects()->delete();
            }

            if ($report->journeys()->exists()) {
                $report->journeys()->delete();
            }

            if ($report->accessDatas()->exists()) {
                $report->accessDatas()->delete();
            }

            $report->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
