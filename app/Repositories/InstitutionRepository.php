<?php

namespace App\Repositories;

use App\Models\Institution;
use App\Interfaces\InstitutionRepositoryInterface;

class InstitutionRepository implements InstitutionRepositoryInterface
{
    public function getAllForDatatable()
    {
        return Institution::select(['id', 'name', 'type', 'created_at']);
    }

    public function getAllOrderedByName()
    {
        return Institution::orderBy('name')->get();
    }

    public function getDistinctTypes()
    {
        return Institution::select('type')->distinct()->pluck('type');
    }

    public function store(array $data)
    {
        return Institution::create($data);
    }

    public function findById($id)
    {
        return Institution::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $institution = Institution::findOrFail($id);
        return $institution->update($data);
    }

    public function delete($id)
    {
        $institution = Institution::findOrFail($id);
        return $institution->delete();
    }
}