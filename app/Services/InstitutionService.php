<?php

namespace App\Services;

use App\Repositories\InstitutionRepository;
use Illuminate\Support\Facades\DB;

class InstitutionService
{
    protected $institutionRepository;

    public function __construct(InstitutionRepository $institutionRepository)
    {
        $this->institutionRepository = $institutionRepository;
    }

    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $this->institutionRepository->store($data);
            DB::commit();
            return ['status' => true, 'message' => 'Institusi berhasil dibuat.'];
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Gagal membuat institusi: ' . $e->getMessage()];
        }
    }

    public function update($id, array $data)
    {
        DB::beginTransaction();
        try {
            $this->institutionRepository->update($id, $data);
            DB::commit();
            return ['status' => true, 'message' => 'Institusi berhasil diperbarui.'];
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Gagal memperbarui institusi: ' . $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->institutionRepository->delete($id);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getAllForDatatable()
    {
        return $this->institutionRepository->getAllForDatatable();
    }

    public function getTypes()
    {
        return $this->institutionRepository->getDistinctTypes();
    }

    public function getById($id)
    {
        return $this->institutionRepository->findById($id);
    }
}
