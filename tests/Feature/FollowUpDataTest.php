<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportFollowUp;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FollowUpDataTest extends TestCase
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

    public function test_user_can_store_follow_up_notes(): void
    {
        $user = User::factory()->create();

        $category = ReportCategory::create([
            'name' => 'Disiplin',
        ]);

        $report = Report::create([
            'title' => 'Laporan Pelanggaran',
            'description' => 'Detail laporan pelanggaran.',
            'incident_datetime' => now(),
            'category_id' => $category->id,
            'status' => 'PEMERIKSAAN',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('reports.followups.store', $report->id), [
            'notes' => 'Catatan internal untuk tindak lanjut laporan.',
        ]);

        $response->assertRedirect(route('reports.show', $report->id));
        $response->assertSessionHas('success', 'Catatan tindak lanjut berhasil ditambahkan.');

        $this->assertDatabaseCount('report_follow_ups', 1);
        $this->assertDatabaseHas('report_follow_ups', [
            'report_id' => $report->id,
            'user_id' => $user->id,
        ]);

        $followUp = ReportFollowUp::first();
        $this->assertNotNull($followUp);
        $this->assertEquals('Catatan internal untuk tindak lanjut laporan.', $followUp->notes);

        $detailResponse = $this->get(route('reports.show', $report->id));
        $detailResponse->assertOk();
        $detailResponse->assertSee('Swal.fire', false);
        $detailResponse->assertSee('Catatan tindak lanjut berhasil ditambahkan.', false);
        $detailResponse->assertSee($followUp->notes, false);
        $detailResponse->assertSee($user->name, false);
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

        if (! Schema::hasTable('report_follow_ups')) {
            throw new \RuntimeException('Failed to create report_follow_ups table');
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
            database_path('migrations/2025_11_12_000000_create_report_follow_ups_table.php'),
        ];
    }
}
