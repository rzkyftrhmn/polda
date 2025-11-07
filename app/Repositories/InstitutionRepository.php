<?php

namespace App\Repositories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class InstitutionRepository
{
    protected function query(): Builder
    {
        return Institution::query();
    }

    public function getAllForDatatable(): Builder
    {
        return $this->query()->select('institutions.*');
    }

    public function getDistinctTypes(): Collection
    {
        return $this->query()->select('type')->distinct()->pluck('type');
    }

    public function findById($id): ?Institution
    {
        return $this->query()->find($id);
    }

    public function store(array $payload): Institution
    {
        return Institution::create($payload);
    }

    public function update($id, array $payload): ?Institution
    {
        $institution = $this->findById($id);
        if (!$institution) return null;
        $institution->update($payload);
        return $institution->refresh();
    }

    public function delete($id): bool
    {
        $institution = $this->findById($id);
        return $institution ? (bool)$institution->delete() : false;
    }
}
