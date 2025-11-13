<?php

namespace App\Repositories;

use App\Models\Division;
use App\Interfaces\SubDivisionRepositoryInterface;

class SubDivisionRepository implements SubDivisionRepositoryInterface
{
    // Query untuk DataTables
    public function getDataTableQuery()
    {
        return Division::with('parent')
        ->whereNotNull('parent_id')
        ->select(['id', 'name', 'type', 'parent_id', 'created_at']);
    }

    // Mengembalikan semua division diurutkan berdasarkan nama
    public function getAllOrderedByName()
    {
        return Division::orderBy('name')->get();
    }

    // Simpan data division
    public function store(array $data)
    {
        return Division::create($data);
    }

    // Cari berdasarkan ID
    public function findById($id)
    {
        return Division::findOrFail($id);
    }

    // Update division
    public function update($id, array $data)
    {
        $division = Division::findOrFail($id);
        return $division->update($data);
    }

    // Hapus division
    public function delete($id)
    {
        $division = Division::findOrFail($id);
        return $division->delete();
    }
}
