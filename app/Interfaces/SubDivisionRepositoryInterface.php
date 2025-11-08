<?php

namespace App\Interfaces;

interface SubDivisionRepositoryInterface
{
    /**
     * Return all divisions ordered by name.
     */
    public function getDataTableQuery();
    public function getAllOrderedByName();
    public function store(array $data);
    public function findById($id);
    public function update($id, array $data);
    public function delete($id);
}