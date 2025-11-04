<?php

namespace App\Repositories;

use App\Interfaces\ProfileRepositoryInterface;
use App\Models\User;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function __construct(private User $model) {}

    public function findById($id)
    {
        return $this->model->newQuery()
            ->with(['institution', 'division'])
            ->findOrFail($id);
    }

    public function update($id, array $data)
    {
        $user = $this->model->findOrFail($id);
        $user->update($data);
        return $user;
    }
}
