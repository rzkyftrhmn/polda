<?php

namespace App\Repositories;

use App\Models\User;
use App\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function query()
    {
        return new User();
    }

    public function getAllRaw()
    {
        return $this->query()->orderBy('created_at', 'desc');
    }

    /**
     * Provide base query with joins to institutions and divisions for datatables.
     */
    public function getAllForDatatable()
    {
        return $this->query()
            ->leftJoin('institutions', 'institutions.id', '=', 'users.institution_id')
            ->leftJoin('divisions', 'divisions.id', '=', 'users.division_id')
            ->select('users.*', 'institutions.name as institution_name', 'divisions.name as division_name');
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