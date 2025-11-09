<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Repositories\PermissionRepository;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $repo = new PermissionRepository();
        $repo->getAllPermissions(); // insert permission ke DB
    }
}
