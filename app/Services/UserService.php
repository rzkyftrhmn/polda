<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private $repo;
    private $institutionRepo;
    private $divisionRepo;
    private $roleRepo;
    public function __construct(
        UserRepositoryInterface $repo,
        \App\Interfaces\InstitutionRepositoryInterface $institutionRepo,
        \App\Interfaces\DivisionRepositoryInterface $divisionRepo,
        RoleRepositoryInterface $roleRepo,
    ) {
        $this->repo = $repo;
        $this->institutionRepo = $institutionRepo;
        $this->divisionRepo = $divisionRepo;
        $this->roleRepo = $roleRepo;
    }

    public function getAllRaw()
    {
        return $this->repo->getAllRaw();
    }

    public function getAllForDatatable()
    {
        return $this->repo->getAllForDatatable();
    }

    public function getInstitutions()
    {
        return $this->institutionRepo->getAllOrderedByName();
    }

    public function getDivisions()
    {
        return $this->divisionRepo->getAllOrderedByName();
    }

    public function getRoles()
    {
        return $this->roleRepo->getAllOrderedByName();
    }

    public function store($data)
    {
        // Pastikan password di-hash sebelum proses store
        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->repo->store($data);
        // Assign role if provided (using Spatie's roles by name)
        if ($user && isset($data['role']) && !empty($data['role'])) {
            $user->assignRole($data['role']);
        }
        return [
            'status' => (bool) $user,
            'message' => 'User created successfully',
            'data' => $user,
        ];
    }

    public function update($id, $data)
    {
        // Hash password jika diberikan, hindari overwrite jika kosong
        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $updated = $this->repo->update($id, $data);

        // Fetch the user and sync role if provided
        $user = $this->repo->findById($id);
        if ($user && isset($data['role']) && !empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return [
            'status' => (bool) $updated,
            'message' => 'User updated successfully',
            'data' => $user,
        ];
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    public function getById($id)
    {
        return $this->repo->findById($id);
    }
}