<?php

namespace App\Services;

use App\Interfaces\PermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class PermissionService
{
    protected $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function getAllForDatatable()
    {
        return $this->permissionRepository->getAllRaw();
    }


    public function getAll()
    {
        return $this->permissionRepository->getAllRaw()->get();
    }

    public function store($data)
    {
        DB::beginTransaction();
        try {
            $permission = $this->permissionRepository->store($data);
            DB::commit();
            return $permission;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getById($id)
    {
        return $this->permissionRepository->getById($id);
    }

    public function update($id, $data)
    {
        DB::beginTransaction();
        try {
            $result = $this->permissionRepository->update($id, $data);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $result = $this->permissionRepository->delete($id);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
