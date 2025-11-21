<?php

namespace App\Services;

use App\Enums\ReportJourneyType;
use App\Repositories\PelaporanRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Report;
use App\Models\AccessData;

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
            'created_by' => auth()->id(),
            'status' => ReportJourneyType::SUBMITTED->value,
            'division_id' => auth()->user()->division_id, 
            'code' => $this->generateReportCode(),
        ]);

        $this->repo->createJourney([
            'report_id' => $report->id,
            'division_id' => auth()->user()->division_id,
            'type' => 'SUBMITTED',
            'description' => $data['description'],
        ]);

        foreach ($data['suspects'] as $suspect) {
            $suspectDivision = $suspect['satker_id']
                ?? $suspect['satwil_id']
                ?? auth()->user()->division_id;

            $this->repo->createSuspect([
                'report_id'   => $report->id,
                'name'        => $suspect['name'],
                'division_id' => $suspectDivision,
            ]);

            // Tambahkan access hanya jika division beda
            if ($suspectDivision != auth()->user()->division_id) {
                $this->repo->createAccess([
                    'report_id'  => $report->id,
                    'division_id'=> $suspectDivision,
                    'is_finish'  => false,
                ]);
            }
        }



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
        $user = auth()->user();

        if (strtolower($user->getRoleNames()->first()) === 'admin') {
            return Report::with(['province', 'city', 'district']);
        }

        $userReportIds = Report::where('created_by', $user->id)
            ->pluck('id')
            ->toArray();

        $allowedReportIds = AccessData::where('division_id', $user->division_id)
            ->pluck('report_id')
            ->toArray();

        $finalIds = array_unique(array_merge($userReportIds, $allowedReportIds));

        // logger("User Report IDs: " . json_encode($userReportIds));
        // logger("Allowed Report IDs: " . json_encode($allowedReportIds));
        // logger("Final Report IDs after merge: " . json_encode($finalIds));
        // logger("User Division ID: " . $user->division_id);

        if (empty($finalIds)) {
            return Report::whereRaw('1=0');
        }

        return Report::with(['province', 'city', 'district'])
            ->whereIn('id', $finalIds)
            ->orderBy('created_at', 'desc');
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

    public function getById($id)
    {
        return $this->repo->find($id);
    }

    public function update($id, array $data)
    {
        DB::beginTransaction();

        try {

            // Ambil laporan beserta relasi
            $report = $this->repo->find($id);
            if (!$report) {
                throw new \Exception("Laporan tidak ditemukan.");
            }

            // ========== UPDATE DATA REPORT ==========
            $this->repo->updateReport($report, [
                'title'               => $data['title'],
                'incident_datetime'   => $data['incident_datetime'],
                'category_id'         => $data['category_id'],
                'description'         => $data['description'],
                'city_id'             => $data['city_id'],
                'district_id'         => $data['district_id'],
                'address_detail'      => $data['address_detail'],
                'name_of_reporter'    => $data['name_of_reporter'],
                'address_of_reporter' => $data['address_of_reporter'],
                'phone_of_reporter'   => $data['phone_of_reporter'],
            ]);

            // ========== UPDATE SUSPECTS ==========
            // HAPUS semua suspects lama
            $report->suspects()->delete();

            // TAMBAHKAN suspects baru
            if (!empty($data['suspects'])) {
                foreach ($data['suspects'] as $suspect) {
                    $divisionId = $suspect['division_id']
                        ?? $suspect['satker_id']
                        ?? $suspect['satwil_id']
                        ?? null;

                    $this->repo->createSuspect([
                        'report_id'   => $report->id,
                        'name'        => $suspect['name'],
                        'division_id' => $divisionId,
                    ]);
                }
            }

            // ========== UPDATE ACCESS DATA ==========
            // Ambil division utama
            $divisionId = $data['division_id']
                ?? ($data['suspects'][0]['division_id'] ?? null)
                ?? auth()->user()->division_id;

            if (!$divisionId) {
                throw new \Exception("Division ID tidak ditentukan.");
            }

            // HAPUS access lama
            $report->accessDatas()->delete();

            // INSERT access baru
            $this->repo->createAccess([
                'report_id'  => $report->id,
                'division_id'=> auth()->user()->division_id,
                'is_finish'  => false,
            ]);

            // ========== UPDATE JOURNEY ==========
            $report->journeys()->create([
                'division_id' => auth()->user()->division_id,
                'type'        => 'SUBMITTED',
                'description' => $data['description'],
            ]);

            DB::commit();
            return $report;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getRelatedUsersForReport(Report $report): Collection
    {
        $divisionIds = $report->accessDatas->pluck('division_id')->filter()->unique()->values()->all();
        $accessUsers = $this->repo->getUsersByDivisionIds($divisionIds);
        $adminUsers = $this->repo->getAdminUsers();
        return $accessUsers->merge($adminUsers)->unique('id')->values();
    }

    public function getInstructionsForReport(int $reportId): Collection
    {
        return $this->repo->getInstructionsByReportId($reportId);
    }

    public function getUserMapForInstructions(Collection $instructions): Collection
    {
        $userIds = $instructions->flatMap(function ($i) {
            return [optional($i)->user_id_from, optional($i)->user_id_to];
        })->filter()->unique()->values()->all();

        $users = $this->repo->getUsersByIds($userIds);
        return $users->keyBy('id');
    }

    public function storeInstruction(int $reportId, int $fromUserId, int $toUserId, string $message)
    {
        return $this->repo->createInstruction([
            'report_id' => $reportId,
            'user_id_from' => $fromUserId,
            'user_id_to' => $toUserId,
            'message' => $message,
        ]);
    }


}
