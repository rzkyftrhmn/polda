<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;
use App\Interfaces\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    public function query()
    {
        return new Role();
    }

    public function getAllOrderedByName()
    {
        return $this->query()->orderBy('name')->get();
    }
}