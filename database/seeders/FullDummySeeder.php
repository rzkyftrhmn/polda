<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FullDummySeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear tables
        DB::table('divisions')->truncate();
        DB::table('institutions')->truncate();
        DB::table('report_categories')->truncate();
        DB::table('reports')->truncate();
        DB::table('report_journeys')->truncate();
        DB::table('report_evidences')->truncate();
        DB::table('suspects')->truncate();
        DB::table('users')->truncate();

        // ==========================================
        // DIVISIONS
        // ==========================================
        DB::table('divisions')->insert([
            ['id'=>1, 'parent_id'=>null, 'name'=>'Reskrim', 'type'=>'polres', 'created_at'=>'2025-11-05 21:36:43'],
            ['id'=>2, 'parent_id'=>null, 'name'=>'Intelkam', 'type'=>'polres', 'created_at'=>'2025-11-05 21:36:43'],
            ['id'=>3, 'parent_id'=>null, 'name'=>'Umum', 'type'=>'polres', 'created_at'=>'2025-11-05 21:36:43'],
            ['id'=>4, 'parent_id'=>1, 'name'=>'Sat Reskrim', 'type'=>'satuan', 'created_at'=>'2025-11-05 21:36:43'],
            ['id'=>5, 'parent_id'=>3, 'name'=>'Subbag Umum', 'type'=>'subbag', 'created_at'=>'2025-11-05 21:36:43'],
            ['id'=>7, 'parent_id'=>null, 'name'=>'Lebong', 'type'=>'polda', 'created_at'=>'2025-11-13 01:53:25'],
        ]);

        // ==========================================
        // INSTITUTIONS
        // ==========================================
        DB::table('institutions')->insert([
            ['id'=>1,'name'=>'Polda Metro Jaya','type'=>'polda','created_at'=>'2025-11-05 21:36:43'],
            ['id'=>2,'name'=>'Polda Jawa Barat','type'=>'polda','created_at'=>'2025-11-05 21:36:43'],
            ['id'=>3,'name'=>'Polres Jakarta Selatan','type'=>'polda','created_at'=>'2025-11-05 21:36:43'],
            ['id'=>4,'name'=>'Polres Bandung','type'=>'polres','created_at'=>'2025-11-05 21:36:43'],
        ]);

        // ==========================================
        // REPORT CATEGORIES
        // ==========================================
        DB::table('report_categories')->insert([
            ['id'=>1,'name'=>'satu'],
            ['id'=>2,'name'=>'dua']
        ]);

        // ==========================================
        // REPORTS
        // ==========================================
        DB::table('reports')->insert([
            [
                'id'=>1,
                'code'=>'RPT-G8IYTJ',
                'title'=>'Laporan Disiplin Anggota',
                'description'=>'Pelanggaran ringan terkait keterlambatan anggota dalam apel pagi.',
                'finish_time'=>1763149790,
                'incident_datetime'=>'2025-11-10 02:14:07',
                'province_id'=>1,'city_id'=>1,'district_id'=>1,
                'address_detail'=>'Jl. Merdeka No. 45, Jakarta Pusat',
                'category_id'=>1,
                'status'=>'SELESAI',
                'created_at'=>'2025-11-11 19:14:07',
                'updated_at'=>'2025-11-14 12:49:50'
            ],
            [
                'id'=>2,
                'code'=>'RPT-I2NZ38',
                'title'=>'Laporan Etika Pelayanan Publik',
                'description'=>'Anggota bersikap tidak sopan terhadap masyarakat di SPKT.',
                'finish_time'=>null,
                'incident_datetime'=>'2025-11-08 02:14:07',
                'province_id'=>1,'city_id'=>2,'district_id'=>3,
                'address_detail'=>'SPKT Polres Metro Jakarta Selatan',
                'category_id'=>2,
                'status'=>'LIMPAH',
                'created_at'=>'2025-11-11 19:14:07',
                'updated_at'=>'2025-11-12 12:02:46'
            ],
            [
                'id'=>3,
                'code'=>'RPT-N2EHD1',
                'title'=>'Laporan Penyelesaian Kasus Etik',
                'description'=>'Kasus etik telah selesai diproses dan dinyatakan tuntas.',
                'finish_time'=>1762913647,
                'incident_datetime'=>'2025-11-05 02:14:07',
                'province_id'=>2,'city_id'=>4,'district_id'=>2,
                'address_detail'=>'Gedung Etik Polda Metro Jaya',
                'category_id'=>1,
                'status'=>'SIDANG',
                'created_at'=>'2025-11-11 19:14:07',
                'updated_at'=>'2025-11-14 07:45:04'
            ],
        ]);

        // ==========================================
        // REPORT JOURNEYS
        // ==========================================
        DB::table('report_journeys')->insert([
            [
                'id'=>22,'report_id'=>1,'institution_id'=>1,'division_id'=>4,
                'type'=>'PEMERIKSAAN',
                'description'=>'{"text":"dsdada"}',
                'created_at'=>'2025-11-14 12:37:51'
            ],
            [
                'id'=>23,'report_id'=>1,'institution_id'=>1,'division_id'=>4,
                'type'=>'LIMPAH',
                'description'=>'{"text":"sdsa"}',
                'created_at'=>'2025-11-14 12:38:10'
            ],
            [
                'id'=>29,'report_id'=>1,'institution_id'=>1,'division_id'=>4,
                'type'=>'SELESAI',
                'description'=>'{"text":"dddsadsadasd"}',
                'created_at'=>'2025-11-14 12:49:50'
            ],
        ]);

        // ==========================================
        // REPORT EVIDENCES
        // ==========================================
        DB::table('report_evidences')->insert([
            [
                'id'=>33,'report_journey_id'=>20,'report_id'=>3,
                'file_url'=>'/storage/evidences/aqloLBczJCUwjukNDD7eRuDDWGzgeJistoKtkHsM.docx',
                'file_type'=>'docx','created_at'=>'2025-11-14 07:45:04'
            ],
            [
                'id'=>35,'report_journey_id'=>22,'report_id'=>1,
                'file_url'=>'/storage/evidences/1763149071-Laporan_Tugas_KNN.docx',
                'file_type'=>'docx','created_at'=>'2025-11-14 12:37:51'
            ],
            [
                'id'=>36,'report_journey_id'=>23,'report_id'=>1,
                'file_url'=>'/storage/evidences/1763149090-LaporanPemrosesanData.docx',
                'file_type'=>'docx','created_at'=>'2025-11-14 12:38:10'
            ],
            [
                'id'=>40,'report_journey_id'=>29,'report_id'=>1,
                'file_url'=>'/storage/evidences/1763149790-wo1iZ3chYC37eMEBzpTZNTG0ui1IF03pCp8lKPXj.png',
                'file_type'=>'png','created_at'=>'2025-11-14 12:49:50'
            ],
        ]);

        // ==========================================
        // SUSPECTS
        // ==========================================
        DB::table('suspects')->insert([
            ['id'=>1,'report_id'=>1,'name'=>'jajang','description'=>'dasdasdasd']
        ]);

        // ==========================================
        // USERS
        // ==========================================
        DB::table('users')->insert([
            [
                'id'=>1,
                'username'=>'busdamri',
                'name'=>'user',
                'email'=>'user@mail.com',
                'institution_id'=>1,
                'division_id'=>4,
                'password'=>Hash::make('password'),
                'photo'=>'profile/ApuZ1SxSRAqa4UDYWPacNr8yFkq2pOcjAjLBTqfo.png',
                'created_at'=>'2025-11-05 21:36:43'
            ]
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
