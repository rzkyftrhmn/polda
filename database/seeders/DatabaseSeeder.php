<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(InstitutionTableSeeder::class);
        $this->call(DivisionTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(ReportCategorySeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(ReportSeeder::class);
    }
}
