<?php

namespace App\Repositories;

use App\Models\Institution;
use App\Interfaces\InstitutionRepositoryInterface;

class InstitutionRepository implements InstitutionRepositoryInterface
{
    public function query()
    {
        return new Institution();
    }

    public function getAllOrderedByName()
    {
        return $this->query()->orderBy('name')->get();
    }
}