<?php

namespace App\Repositories;

use Spatie\Permission\Models\Permission;
use App\Interfaces\PermissionRepositoryInterface;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function query()
    {
        return new Permission();    
    }

    public function getAllRaw()
    {
        return $this->query()->orderBy('created_at', 'desc');
    }

    public function store($payload)
    {
        return $this->query()->create($payload);
    }

    public function findById($id)
    {
        return $this->query()->find($id);
    }

    public function update($id, $payload)
    {
        return $this->query()->find($id)->update($payload);
    }

    public function delete($id)
    {
        return $this->query()->find($id)->delete();
    }
}