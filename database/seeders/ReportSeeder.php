<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('reports')->truncate();

        $reports = [
            [
                'code' => 'RPT-' . strtoupper(Str::random(6)),
                'title' => 'Laporan Disiplin Anggota',
                'description' => 'Pelanggaran ringan terkait keterlambatan anggota dalam apel pagi.',
                'finish_time' => null,
                'incident_datetime' => now()->subDays(2)->toDateTimeString(),
                'province_id' => 1,
                'city_id' => 1,
                'district_id' => 1,
                'address_detail' => 'Jl. Merdeka No. 45, Jakarta Pusat',
                'category_id' => 1,
                'status' => 'PEMERIKSAAN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'RPT-' . strtoupper(Str::random(6)),
                'title' => 'Laporan Etika Pelayanan Publik',
                'description' => 'Anggota bersikap tidak sopan terhadap masyarakat saat bertugas di SPKT.',
                'finish_time' => null,
                'incident_datetime' => now()->subDays(4)->toDateTimeString(),
                'province_id' => 1,
                'city_id' => 2,
                'district_id' => 3,
                'address_detail' => 'SPKT Polres Metro Jakarta Selatan',
                'category_id' => 2,
                'status' => 'LIMPAH',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'RPT-' . strtoupper(Str::random(6)),
                'title' => 'Laporan Penyelesaian Kasus Etik',
                'description' => 'Kasus etik telah selesai diproses dan dinyatakan tuntas.',
                'finish_time' => now()->timestamp, // ← integer UNIX timestamp
                'incident_datetime' => now()->subDays(7)->toDateTimeString(),
                'province_id' => 2,
                'city_id' => 4,
                'district_id' => 2,
                'address_detail' => 'Gedung Etik Polda Metro Jaya',
                'category_id' => 1,
                'status' => 'SELESAI',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('reports')->insert($reports);

        echo "✅ ReportSeeder berhasil (finish_time pakai UNIX timestamp)\n";
    }
}
