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
        // Parents
        $reskrim = Division::updateOrCreate(
            ['name' => 'Reskrim'],
            ['parent_id' => null, 'type' => 'polres']
        );
        $intelkam = Division::updateOrCreate(
            ['name' => 'Intelkam'],
            ['parent_id' => null, 'type' => 'polres']
        );
        $umum = Division::updateOrCreate(
            ['name' => 'Umum'],
            ['parent_id' => null, 'type' => 'polres']
        );

        // Children
        Division::updateOrCreate(
            ['name' => 'Sat Reskrim'],
            ['parent_id' => $reskrim->id, 'type' => 'satuan']
        );
        Division::updateOrCreate(
            ['name' => 'Subbag Umum'],
            ['parent_id' => $umum->id, 'type' => 'subbag']
        );
        Division::updateOrCreate(
            ['name' => 'Sat Intelkam'],
            ['parent_id' => $intelkam->id, 'type' => 'satuan']
        );
    }
}