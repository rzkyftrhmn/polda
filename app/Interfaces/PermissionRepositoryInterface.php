<?php

namespace App\Interfaces;

interface PermissionRepositoryInterface
{
    public function getAllPermissions();
    public function AdminPermission();
    public function PoldaPermission();
    public function PolresPermission();
    public function KasubbidPermission();
    public function getAllRaw();
    public function getById($id);
    public function getPermissionByName($name);
    public function getPermissionByNameExcludeId($name, $id);
    public function store($payload);
    public function update($id, $payload);
    public function delete($id);
}