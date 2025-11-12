<?php

namespace App\Services;

use App\Interfaces\ReportJourneyRepositoryInterface;
use App\Models\ReportEvidence;
<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
=======
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;
>>>>>>> 02a3e64 (test: verify journey multi-upload success)

class ReportJourneyService
{
    public function __construct(
        protected ReportJourneyRepositoryInterface $repository
    ) {
    }

<<<<<<< HEAD
    public function store(array $data, array $files = [])
    {
        return DB::transaction(function () use ($data, $files) {
            $journey = $this->repository->store($data);

            foreach ($files as $file) {
=======
    public function store(array $data, array $files = []): array
    {
        DB::beginTransaction();

        try {
            $journey = $this->repository->store($data);

            foreach ($files as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

>>>>>>> 02a3e64 (test: verify journey multi-upload success)
                $storedPath = $file->store('evidences', 'public');

                ReportEvidence::create([
                    'report_journey_id' => $journey->id,
                    'report_id' => $journey->report_id,
                    'file_url' => Storage::url($storedPath),
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }

<<<<<<< HEAD
            return $journey;
        });
=======
            DB::commit();

            return [
                'status' => true,
                'message' => 'Tahapan penanganan berhasil ditambahkan.',
                'data' => $journey->load('evidences'),
            ];
        } catch (Throwable $throwable) {
            DB::rollBack();

            report($throwable);

            return [
                'status' => false,
                'message' => 'Gagal menambahkan tahapan penanganan.',
            ];
        }
>>>>>>> 02a3e64 (test: verify journey multi-upload success)
    }
}
