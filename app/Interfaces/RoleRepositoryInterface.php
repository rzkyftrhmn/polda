<?php

namespace App\Interfaces;

interface RoleRepositoryInterface
{
    public function getAllForDatatable();
    public function getAllOrderedByName();
    public function store(array $data);
    public function findById($id);
    public function update($id, array $data);
    public function delete($id);
    public function getAllPermissions();
}