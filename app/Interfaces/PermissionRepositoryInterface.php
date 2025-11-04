<?php

namespace App\Interfaces;

interface PermissionRepositoryInterface
{
    public function getAllRaw();
    public function store($payload);
    public function findById($id);
    public function update($id, $payload);
    public function delete($id);
}