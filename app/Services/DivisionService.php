<?php

namespace App\Services;

use App\Repositories\DivisionRepository;
use Illuminate\Support\Facades\DB;

class DivisionService
{
    protected $divisionRepository;

    public function __construct(DivisionRepository $divisionRepository)
    {
        $this->divisionRepository = $divisionRepository;
    }

    public function store(array $data)
    {
        DB::beginTransaction();

        try {
            $division = $this->divisionRepository->store($data);
            DB::commit();
            return ['status' => true, 'message' => 'Divisi berhasil dibuat.'];
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Gagal membuat divisi: ' . $e->getMessage()];
        }
    }

    public function update($id, array $data)
    {
        DB::beginTransaction();

        try {
            $this->divisionRepository->update($id, $data);
            DB::commit();
            return ['status' => true, 'message' => 'Divisi berhasil diperbarui.'];
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Gagal memperbarui divisi: ' . $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->divisionRepository->delete($id);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
