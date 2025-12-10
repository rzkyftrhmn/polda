<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSubBagRehapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Polda Jawa Barat institution for assignment
        $poldaJabar = Institution::where('name', 'Polda Jawa Barat')->first();
        
        $adminsubBagRehab = User::updateOrCreate(
            ['email' => 'subbanrehab@arsipberkas-propam.id'],
            [
                'institution_id' => $poldaJabar?->id,
                'division_id' => null,
                'username' => 'subbanrehab_polda',
                'name' => 'Sub Bagian Rehabilitasi Polda Jawa Barat',
                'password' => Hash::make('ArsipPropam@2025'),
                'email_verified_at' => now(),
            ]
        );
        $adminsubBagRehab->assignRole(ROLE_SUB_BAG_REHAB);
    }
}
