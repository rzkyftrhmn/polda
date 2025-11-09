<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleService
{
    
    public function getAllForDatatable()
    {
        return Role::query()->select(['id', 'name', 'guard_name', 'created_at']);
    }

    public function getAllPermissions()
    {
        return Permission::orderBy('name')->get();
    }

    public function getById($id)
    {
        return Role::with('permissions')->find($id);
    }
    
    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web',
            ]);

            if (!empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            DB::commit();
            return [
                'status' => true,
                'message' => 'Role berhasil dibuat.',
                'data' => $role,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('RoleService@store', ['error' => $e->getMessage()]);
            return [
                'status' => false,
                'message' => 'Gagal membuat role: ' . $e->getMessage(),
            ];
        }
    }

    public function update($id, array $data)
    {
        DB::beginTransaction();
        try {
            $role = Role::find($id);
            if (!$role) {
                return ['status' => false, 'message' => 'Role tidak ditemukan.'];
            }

            $role->update([
                'name' => $data['name'],
            ]);

            // sync permissions
            if (!empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            DB::commit();
            return [
                'status' => true,
                'message' => 'Role berhasil diperbarui.',
                'data' => $role,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('RoleService@update', ['error' => $e->getMessage()]);
            return [
                'status' => false,
                'message' => 'Gagal memperbarui role: ' . $e->getMessage(),
            ];
        }
    }

    public function delete($id)
    {
        $role = Role::find($id);
        if (!$role) {
            logger()->error("Role not found", ['id' => $id]);
            return false;
        }

        try {
            $role->permissions()->detach();

            if (method_exists($role, 'users') && $role->users()->count() > 0) {
                logger()->error("Role still assigned to users", ['id' => $id]);
                return false;
            }

            $role->delete();
            return true;
        } catch (\Throwable $e) {
            logger()->error('RoleService@delete', ['error' => $e->getMessage()]);
            return false;
        }
    }


}
