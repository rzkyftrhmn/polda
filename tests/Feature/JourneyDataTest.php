<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\Institution;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportJourney;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class JourneyDataTest extends TestCase
{
    protected array $loadedMigrations = [];

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->runRequiredMigrations();
    }

    protected function tearDown(): void
    {
        $this->rollbackRequiredMigrations();

        parent::tearDown();
    }

    public function test_user_can_store_journey_with_multiple_evidences(): void
    {
        Storage::fake('public');

        $institution = Institution::create([
            'name' => 'Polda Metro Jaya',
            'type' => 'POLDA',
        ]);

        $division = Division::create([
            'name' => 'Ditreskrimum',
            'type' => 'DITRES',
        ]);

        $user = User::factory()->create([
            'institution_id' => $institution->id,
            'division_id' => $division->id,
        ]);

        $category = ReportCategory::create([
            'name' => 'Disiplin',
        ]);

        $report = Report::create([
            'title' => 'Pelanggaran Disiplin Anggota',
            'description' => 'Laporan lengkap mengenai pelanggaran disiplin.',
            'incident_datetime' => now(),
            'category_id' => $category->id,
            'status' => 'PEMERIKSAAN',
        ]);

        $files = [
            UploadedFile::fake()->create('evidence-1.pdf', 100),
            UploadedFile::fake()->image('evidence-2.jpg'),
            UploadedFile::fake()->create('evidence-3.docx', 50),
        ];

        $this->actingAs($user);

        $response = $this->post(route('reports.journeys.store', $report->id), [
            'type' => 'PEMERIKSAAN',
            'description' => 'Melakukan pemeriksaan awal terhadap laporan.',
            'files' => $files,
        ]);

        $response->assertRedirect(route('reports.show', $report->id));
        $response->assertSessionHas('success', 'Tahapan penanganan berhasil ditambahkan.');

        $this->assertDatabaseCount('report_journeys', 2);
        $this->assertDatabaseHas('report_journeys', [
            'report_id' => $report->id,
            'institution_id' => $user->institution_id,
            'division_id' => $user->division_id,
            'type' => 'PEMERIKSAAN',
        ]);

        $this->assertDatabaseHas('report_journeys', [
            'report_id' => $report->id,
            'type' => 'SUBMITTED',
        ]);

        $this->assertDatabaseCount('report_evidences', 3);

        $journey = ReportJourney::with('evidences')->where('type', 'PEMERIKSAAN')->first();
        $this->assertNotNull($journey);
        $this->assertEquals(3, $journey->evidences->count());
        $this->assertEquals('Melakukan pemeriksaan awal terhadap laporan.', $journey->description);

        foreach ($journey->evidences as $evidence) {
            $relativePath = Str::after($evidence->file_url, '/storage/');
            Storage::disk('public')->assertExists($relativePath);
        }

        $detailResponse = $this->get(route('reports.show', $report->id));
        $detailResponse->assertOk();
        $detailResponse->assertSee('PENYELIDIKAN', false);
        $detailResponse->assertSee($journey->description, false);

        foreach ($journey->evidences as $evidence) {
            $detailResponse->assertSee(basename($evidence->file_url), false);
        }
    }

    protected function runRequiredMigrations(): void
    {
        foreach ($this->migrationFiles() as $file) {
            $migration = require $file;
            $migration->up();
            $this->loadedMigrations[] = $migration;
        }

        if (Schema::hasTable('report_evidence') && ! Schema::hasTable('report_evidences')) {
            Schema::rename('report_evidence', 'report_evidences');
        }

        if (! Schema::hasTable('report_evidences')) {
            throw new \RuntimeException('Failed to create report_evidences table');
        }
    }

    protected function rollbackRequiredMigrations(): void
    {
        foreach (array_reverse($this->loadedMigrations) as $migration) {
            if (method_exists($migration, 'down')) {
                $migration->down();
            }
        }

        $this->loadedMigrations = [];
    }

    protected function migrationFiles(): array
    {
        return [
            database_path('migrations/2014_10_12_000000_create_users_table.php'),
            database_path('migrations/2025_11_03_194107_add_column_institution_on_users_table.php'),
            database_path('migrations/2025_11_03_195421_create_institutions_table.php'),
            database_path('migrations/2025_11_03_195432_create_divisions_table.php'),
            database_path('migrations/2025_11_10_142551_create_report_categories_table.php'),
            database_path('migrations/2025_11_10_142228_create_reports_table.php'),
            database_path('migrations/2025_11_10_142745_create_report_journeys_table.php'),
            database_path('migrations/2025_11_10_142756_create_report_evidence_table.php'),
            database_path('migrations/2025_11_11_000001_rename_report_evidence_table.php'),
        ];
    }
}
