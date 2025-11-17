<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PARENT DIVISIONS
        $reskrim = Division::updateOrCreate(
            ['name' => 'Reskrim'],
            [
                'parent_id'   => null,
                'type'        => 'polres',
                'level'       => 'parent',
                'permissions' => json_encode(['view', 'create', 'update']),
            ]
        );

        $intelkam = Division::updateOrCreate(
            ['name' => 'Intelkam'],
            [
                'parent_id'   => null,
                'type'        => 'polres',
                'level'       => 'parent',
                'permissions' => json_encode(['view', 'create']),
            ]
        );

        $umum = Division::updateOrCreate(
            ['name' => 'Umum'],
            [
                'parent_id'   => null,
                'type'        => 'polres',
                'level'       => 'parent',
                'permissions' => json_encode(['view']),
            ]
        );

        // CHILDREN DIVISIONS
        Division::updateOrCreate(
            ['name' => 'Sat Reskrim'],
            [
                'parent_id'   => $reskrim->id,
                'type'        => 'satuan',
                'level'       => 'child',
                'permissions' => json_encode(['view', 'create', 'update', 'delete']),
            ]
        );

        Division::updateOrCreate(
            ['name' => 'Sat Intelkam'],
            [
                'parent_id'   => $intelkam->id,
                'type'        => 'satuan',
                'level'       => 'child',
                'permissions' => json_encode(['view', 'update']),
            ]
        );

        Division::updateOrCreate(
            ['name' => 'Subbag Umum'],
            [
                'parent_id'   => $umum->id,
                'type'        => 'subbag',
                'level'       => 'child',
                'permissions' => json_encode(['view']),
            ]
        );
    }
}
