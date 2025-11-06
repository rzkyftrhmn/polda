<?php

namespace App\Repositories;

use App\Models\Role;
use App\Interfaces\RoleRepositoryInterface;
use Spatie\Permission\Models\Permission;

class RoleRepository implements RoleRepositoryInterface
{
    public function getAllForDatatable()
    {
        return Role::with('permissions')->select(['id', 'name', 'created_at']);
    }

    public function getAllOrderedByName()
    {
        return Role::orderBy('name', 'desc')->get();
    }

    public function store(array $data)
    {
        $role = Role::create($data);

        //sync permissions jika ada
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }
        return $role;
    }

    public function findById($id)
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function update($id, array $data)
    {
        $role = Role::findOrFail($id);
        $role->update('name', $data['name']);

        //sync permissions jika ada
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);
        return $role->delete();
    }

    public function getAllPermissions()
    {
        return Permission::all();
    }
}
