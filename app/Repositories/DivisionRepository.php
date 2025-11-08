<?php

namespace App\Repositories;

use App\Models\Division;
use App\Interfaces\DivisionRepositoryInterface;

class DivisionRepository implements DivisionRepositoryInterface
{
    public function getAllOrderedByName()
    {
        return Division::orderBy('name')->get();
    }
}
