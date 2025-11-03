<?php

namespace App\Repositories;

use App\Models\Division;
use App\Interfaces\DivisionRepositoryInterface;

class DivisionRepository implements DivisionRepositoryInterface
{
    public function query()
    {
        return new Division();
    }

    public function getAllOrderedByName()
    {
        return $this->query()->orderBy('name')->get();
    }
}