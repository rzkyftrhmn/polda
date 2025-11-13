<?php

namespace App\Interfaces;

interface ProfileRepositoryInterface
{
    public function findById($id);
    public function update($id, array $data);
}
