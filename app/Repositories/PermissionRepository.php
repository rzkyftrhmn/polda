<?php

namespace App\Repositories;

use App\Interfaces\PermissionRepositoryInterface;
use Spatie\Permission\Models\Permission;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function query()
    {
        return new Permission();    
    }

    public function getAllPermissions()
    {
        $permissions = [
            'user-list', 'user-create', 'user-edit', 'user-delete',
            'role-list', 'role-create', 'role-edit', 'role-delete',
            'instansi-list', 'instansi-create', 'instansi-edit', 'instansi-delete',
            'profile-show', 'profile-edit',
            'pelaporan-list', 'pelaporan-create', 'pelaporan-edit', 'pelaporan-delete',
            'disposisi-list', 'disposisi-create', 'disposisi-edit', 'disposisi-delete',
            'putusan-list', 'putusan-create', 'putusan-edit', 'putusan-delete',
            'report-view', 'report-export',
        ];

        // pastikan permission sudah ada di DB
        foreach ($permissions as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm]);
        }

        return \Spatie\Permission\Models\Permission::all();
    }

    public function AdminPermission()
    {
        return [

            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'instansi-list',
            'instansi-create',
            'instansi-edit',
            'instansi-delete',

            'profile-show',
            'profile-edit',

            'pelaporan-list',
            'pelaporan-create',
            'pelaporan-edit',
            'pelaporan-delete',

            'disposisi-list',
            'disposisi-create',
            'disposisi-edit',
            'disposisi-delete',

            'putusan-list',
            'putusan-create',
            'putusan-edit',
            'putusan-delete',

            'report-view',
            'report-export',
        ];
    }

    public function PoldaPermission()
    {
        return [
            'user-list',
            'role-list',

            'instansi-list',

            'profile-show',
            'profile-edit',

            'pelaporan-list',
            'pelaporan-create',
            'pelaporan-edit',

            'disposisi-list',
            'disposisi-create',
            'disposisi-edit',

            'penyidikan-list',
            'penyidikan-create',
            'penyidikan-edit',

            'putusan-list',
            'putusan-create',

            'report-view',
            'report-export',
        ];
    }

    public function PolresPermission()
    {
        return [
            'profile-show',
            'profile-edit',

            'pelaporan-list',
            'pelaporan-create',
            'pelaporan-edit',

            'disposisi-list',
            'disposisi-create',
            'disposisi-edit',

            'penyidikan-list',
            'penyidikan-create',
            'penyidikan-edit',

            'feedback-list',
            'feedback-create',

            'report-view',
            'report-export',
        ];
    }

    public function KasubbidPermission()
    {
        return [
            'profile-show',
            'profile-edit',

            'pelaporan-list',
            'disposisi-list',
            'penyidikan-list',
            'report-view',
        ];
    }

    public function getAllRaw()
    {
        return $this->query()->orderBy('created_at', 'desc');
    }

    public function getById($id)
    {
        return $this->query()->find($id);
    }

    public function getPermissionByName($name)
    {
        return $this->query()
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();
    }

    public function getPermissionByNameExcludeId($name, $id)
    {
        return $this->query()
            ->whereRaw('LOWER(name) =?', [strtolower($name)])
            ->where('id', '!=', $id)
            ->first();
    }

    public function store($payload)
    {
        return $this->query()->create($payload);
    }

    public function update($id, $payload)
    {
        $permission = $this->query()->find($id);
        return $permission ? $permission->update($payload) : false;
    }

    public function delete($id)
    {
        $permission = Permission::findOrFail($id);
        return $permission->delete();
    }
}
