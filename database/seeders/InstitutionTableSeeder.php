<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institution;

class InstitutionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Polda Metro Jaya', 'type' => 'polda'],
            ['name' => 'Polda Jawa Barat', 'type' => 'polda'],
            ['name' => 'Polres Jakarta Selatan', 'type' => 'polres'],
            ['name' => 'Polres Bandung', 'type' => 'polres'],
        ];

        foreach ($items as $item) {
            Institution::updateOrCreate(
                ['name' => $item['name']],
                ['type' => $item['type']]
            );
        }
    }
}