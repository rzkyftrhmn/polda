<?php

namespace App\Services;

use App\Repositories\SubDivisionRepository;
use Illuminate\Support\Facades\DB;

class SubDivisionService
{
    protected $subDivisionRepository;

    public function __construct(SubDivisionRepository $subDivisionRepository)
    {
        $this->subDivisionRepository = $subDivisionRepository;
    }

    public function store(array $data)
    {
        DB::beginTransaction();

        try {
            $division = $this->subDivisionRepository->store($data);
            DB::commit();
            return ['status' => true, 'message' => 'Sub Divisi berhasil dibuat.'];
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Gagal membuat Sub Divisi: ' . $e->getMessage()];
        }
    }

    public function update($id, array $data)
    {
        DB::beginTransaction();

        try {
            $this->subDivisionRepository->update($id, $data);
            DB::commit();
            return ['status' => true, 'message' => 'Sub Divisi berhasil diperbarui.'];
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Gagal memperbarui Sub Divisi: ' . $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->subDivisionRepository->delete($id);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
