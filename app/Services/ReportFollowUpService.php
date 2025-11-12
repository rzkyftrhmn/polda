<?php

namespace App\Services;

use App\Interfaces\ReportFollowUpRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReportFollowUpService
{
    public function __construct(
        protected ReportFollowUpRepositoryInterface $repository
    ) {
    }

    public function store(array $data): array
    {
        DB::beginTransaction();

        try {
            $followUp = $this->repository->store($data);

            DB::commit();

            return [
                'status' => true,
                'message' => 'Catatan tindak lanjut berhasil ditambahkan.',
                'data' => $followUp->load('user'),
            ];
        } catch (Throwable $throwable) {
            DB::rollBack();

            report($throwable);

            return [
                'status' => false,
                'message' => 'Gagal menambahkan catatan tindak lanjut.',
            ];
        }
    }
}
