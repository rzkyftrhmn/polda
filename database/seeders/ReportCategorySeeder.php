<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportCategory;

class ReportCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Kriminal Umum',
            'Lalu Lintas',
            'Narkoba',
            'Korupsi',
            'Cyber Crime',
            'Kekerasan',
            'Kebakaran',
            'Bencana Alam',
            'Kehilangan',
            'Penipuan',
            'Pencurian',
        ];

        foreach ($categories as $name) {
            ReportCategory::updateOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        }
    }
}