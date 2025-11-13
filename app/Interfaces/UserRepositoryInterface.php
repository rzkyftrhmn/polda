<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getAllRaw();
    /**
     * Base query for Users joined with master tables (institutions, divisions)
     * and selecting alias columns for datatables usage.
     */
    public function getAllForDatatable();
    public function store($payload);
    public function findById($id);
    public function update($id, $payload);
    public function delete($id);
}