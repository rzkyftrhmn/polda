<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin, Polda, Polres, Kasubbid
        $roles = ['Admin', 'Operator', 'Kasubbid', 'SubBagRehab'];
        // update or create roles
        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role],
                ['name' => $role]
            );
        }
    }
}
