<?php

namespace App\Services;

use App\Models\ReportEvidence;
use App\Repositories\JourneyRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JourneyService
{
    protected $journeyRepository;

    public function __construct(JourneyRepository $journeyRepository)
    {
        $this->journeyRepository = $journeyRepository;
    }

    public function store(array $data, $files = [])
    {
        DB::beginTransaction();
        try {
            $journey = $this->journeyRepository->store($data);

            if (!empty($files)) {
                foreach ($files as $file) {
                    $path = $file->store('public/evidences');
                    ReportEvidence::create([
                        'report_journey_id' => $journey->id,
                        'file_url' => Storage::url($path),
                        'file_type' => $file->getClientOriginalExtension(),
                    ]);
                }
            }

            DB::commit();
            return ['status' => true, 'message' => 'Tahapan penanganan dan bukti pendukung berhasil disimpan.'];
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Gagal menyimpan tahapan penanganan: ' . $e->getMessage()];
        }
    }
}
