<?php

namespace App\Interfaces;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
interface InstitutionRepositoryInterface
{
     public function getAllOrderedByName(): Collection;

    public function getAllForDatatable(): Builder;

    public function store(array $payload);

    public function findById($id);

    public function update($id, array $payload);

    public function delete($id): bool;

    // public function getDistinctTypes(): Collection;
}