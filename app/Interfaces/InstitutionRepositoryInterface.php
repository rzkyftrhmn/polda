<?php

namespace App\Interfaces;

interface InstitutionRepositoryInterface
{
    public function getAllForDatatable();
    public function getAllOrderedByName();
    public function getDistinctTypes();
    public function store(array $data);
    public function findById($id);
    public function update($id, array $data);
    public function delete($id);
}