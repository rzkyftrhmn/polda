<?php

namespace App\Services;

use App\Interfaces\ProfileRepositoryInterface;
use App\Interfaces\InstitutionRepositoryInterface;
use App\Interfaces\DivisionRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function __construct(
        private ProfileRepositoryInterface $repo,
        private InstitutionRepositoryInterface $institutionRepo,
        private DivisionRepositoryInterface $divisionRepo
    ) {}

    public function getInstitutions()
    {
        return $this->institutionRepo->getAllOrderedByName();
    }

    public function getDivisions()
    {
        return $this->divisionRepo->getAllOrderedByName();
    }

    public function updateProfile($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function updatePassword($id, string $password)
    {
        return $this->repo->update($id, ['password' => Hash::make($password)]);
    }
}
