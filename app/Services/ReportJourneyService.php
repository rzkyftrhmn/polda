<?php

namespace App\Services;

use App\Enums\ReportJourneyType;
use App\Interfaces\ReportJourneyRepositoryInterface;
use App\Models\AccessData;
use App\Models\Division;
use App\Models\Institution;
use App\Models\Report;
use App\Models\ReportEvidence;
use App\Models\ReportJourney;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ReportJourneyService
{
    public function __construct(
        protected ReportJourneyRepositoryInterface $repository,
        protected NotificationService $notificationService
    ) {
    }

    public function paginateByReport(int $reportId, int $perPage = 5, string $order = 'desc'): LengthAwarePaginator
    {
        $raw = $this->repository->paginateByReport($reportId, $perPage, $order);

        if ($raw instanceof \Illuminate\Pagination\LengthAwarePaginator ||
            $raw instanceof \Illuminate\Pagination\Paginator) {
            $paginator = $raw;
            $journeys = collect($paginator->items());
        } elseif ($raw instanceof \Illuminate\Support\Collection) {
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $raw->forPage(1, $perPage),
                $raw->count(),
                $perPage,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            $journeys = $paginator->getCollection();
        } else {
            throw new RuntimeException('paginateByReport must return paginator or collection');
        }

        $institutionIds = $journeys->pluck('target_institution_id')->filter()->unique();
        $divisionIds    = $journeys->pluck('target_division_id')->filter()->unique();

        $institutions = Institution::whereIn('id', $institutionIds)->get()->keyBy('id');
        $divisions    = Division::whereIn('id', $divisionIds)->get()->keyBy('id');

        $journeys = $journeys->map(function ($journey) use ($institutions, $divisions) {
            $journey->target_institution = $journey->target_institution_id
                ? $institutions[$journey->target_institution_id] ?? null
                : null;

            $journey->target_division = $journey->target_division_id
                ? $divisions[$journey->target_division_id] ?? null
                : null;

            return $journey;
        });

        if (method_exists($paginator, 'setCollection')) {
            $paginator->setCollection($journeys);
        } else {
            $paginator = new LengthAwarePaginator(
                $journeys,
                $paginator->total(),
                $paginator->perPage(),
                $paginator->currentPage(),
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return $paginator;
    }

    public function ensureInitialAccess(Report $report): void
    {
        if ($report->status === ReportJourneyType::COMPLETED->value) {
            return;
        }

        if ($report->accessDatas()->where('is_finish', false)->exists()) {
            return;
        }

        $creatorDivisionId = $report->creator?->division_id
            ?? $report->division_id;

        if ($creatorDivisionId) {
            AccessData::firstOrCreate([
                'report_id' => $report->id,
                'division_id' => $creatorDivisionId,
                'is_finish' => false,
            ]);
        }
    }

    public function hasAccess(?Division $division, Report $report, bool $isAdmin = false): bool
    {
        if ($isAdmin) {
            return true;
        }

        if (!$division) {
            return false;
        }

        $hasAccessRecord = $report->accessDatas()->exists();

        $hasActive = $report->accessDatas()
            ->where('division_id', $division->id)
            ->where('is_finish', false)
            ->exists();

        if ($hasActive) {
            return true;
        }

        // 2. Creator always has access until complete
        if (!$hasAccessRecord && $report->creator && $report->creator->division_id === $division->id) {
            return $report->status !== ReportJourneyType::COMPLETED->value;
        }

        return false;
    }

    public function store(array $data, array $files = []): array
    {
            DB::beginTransaction();

            try {
                $type = $this->normalizeType($data['type'] ?? null);
                $reportId = (int) ($data['report_id'] ?? 0);

                if (!$type || $reportId <= 0) {
                    throw new RuntimeException('Data tahapan tidak lengkap.');
                }

                $report = Report::findOrFail($reportId);

                $description = [
                    'text' => $data['description'] ?? '',
                    'institution_target_id' => $data['institution_target_id'] ?? null,
                    'division_target_id' => $data['division_target_id'] ?? null,
                ];

                $journey = $this->createJourney(
                    $report,
                    $type,
                    $description,
                    $files,
                    $data['division_id'] ?? null,
                    $data['institution_id'] ?? null
                );

                if ($type === ReportJourneyType::TRANSFER) {
                    $this->handleTransferAccess($report, $data['division_id'] ?? null, $journey->target_division_id);
                }

                if ($type === ReportJourneyType::COMPLETED) {
                    $this->finishAccess($report);
                }

                $this->notificationService->notifyReportStatus($journey->report, $type->value);

                DB::commit();

                return [
                    'status' => true,
                    'message' => 'Tahapan penanganan berhasil ditambahkan',
                    'data' => $journey,
                ];
            } catch (Throwable $throwable) {
                DB::rollBack();
                report($throwable);

                return [
                    'status' => false,
                    'message' => 'Gagal menambahkan tahapan penanganan.',
                ];
            }
        }

        public function storeProgress(
        Report $report,
        array $data,
        array $files,
        ?int $divisionId = null,
        ?int $institutionId = null,
        ?int $userId = null
    ): array {

        DB::beginTransaction();

        try {
            $division = $divisionId ? Division::find($divisionId) : null;
            $lastType = $this->lastJourneyType($report);
            $firstFlow = $this->determineFirstFlow($report);

            $flow = $data['flow'] ?? 'inspection';
            $action = $data['action'] ?? 'save';

            // Force flow berdasarkan capability divisi
            if ($division?->canInspection() && !$division?->canInvestigation()) {
                $flow = 'inspection';
            }
            if ($division?->canInvestigation() && !$division?->canInspection()) {
                $flow = 'investigation';
            }

            if ($lastType === ReportJourneyType::COMPLETED) {
                throw new RuntimeException('Laporan sudah selesai.');
            }

            $journeys = [];

            /* ===========================================================
            FLOW 1: INSPECTION
            =========================================================== */
            if ($flow === 'inspection') {

                if ($firstFlow !== 'inspection') {
                    throw new RuntimeException('Tahap pemeriksaan dilewati. Laporan langsung ke penyidikan.');
                }

                if (!in_array($lastType, [
                    ReportJourneyType::SUBMITTED,
                    ReportJourneyType::INVESTIGATION
                ], true)) {
                    throw new RuntimeException('Tahap pemeriksaan tidak valid untuk status laporan ini.');
                }

                $touchStatus = $action !== 'save';

                // Create Investigation journey
                $journeys[] = $this->createJourney(
                    $report,
                    ReportJourneyType::INVESTIGATION,
                    [
                        'text' => 'Dokumen pemeriksaan',
                        'doc_kind' => 'pemeriksaan',
                        'doc_number' => $data['inspection_doc_number'] ?? null,
                        'doc_date' => $data['inspection_doc_date'] ?? null,
                        'conclusion' => $data['inspection_conclusion'] ?? null,
                    ],
                    $files['inspection_files'] ?? [],
                    $divisionId,
                    $institutionId,
                    $touchStatus
                );

                /* === Transfer === */
                if ($action === 'transfer') {
                    $journeys[] = $this->createJourney(
                        $report,
                        ReportJourneyType::TRANSFER,
                        [
                            'text' => 'Laporan dilimpahkan',
                            'doc_kind' => 'pemeriksaan',
                            'doc_number' => $data['inspection_doc_number'] ?? null,
                            'doc_date' => $data['inspection_doc_date'] ?? null,
                            'conclusion' => $data['inspection_conclusion'] ?? null,
                            'decision' => 'LIMPAH',
                            'institution_target_id' => $data['target_institution_id'] ?? null,
                            'division_target_id' => $data['target_division_id'] ?? null,
                        ],
                        [],
                        $divisionId,
                        $institutionId
                    );

                    $this->handleTransferAccess(
                        $report,
                        $divisionId,
                        $data['target_division_id'] ?? null
                    );
                }

                /* === Complete === */
                if ($action === 'complete') {
                    $journeys[] = $this->createJourney(
                        $report,
                        ReportJourneyType::COMPLETED,
                        [
                            'text' => 'Laporan selesai',
                            'doc_kind' => 'pemeriksaan',
                            'doc_number' => $data['inspection_doc_number'] ?? null,
                            'doc_date' => $data['inspection_doc_date'] ?? null,
                            'conclusion' => $data['inspection_conclusion'] ?? null,
                            'decision' => 'SELESAI',
                        ],
                        [],
                        $divisionId,
                        $institutionId
                    );

                    $this->finishAccess($report);
                }
            }

            /* ===========================================================
            FLOW 2: INVESTIGATION
            =========================================================== */
            else {

                $canSkipInspection = (
                    $firstFlow === 'investigation'
                    && $lastType === ReportJourneyType::SUBMITTED
                );

                $allowed = [
                    ReportJourneyType::SUBMITTED,
                    ReportJourneyType::INVESTIGATION,
                    ReportJourneyType::TRANSFER,
                    ReportJourneyType::TRIAL
                ];

                if (!$canSkipInspection && !in_array($lastType, $allowed, true)) {
                    throw new RuntimeException('Laporan belum bisa masuk tahap penyidikan.');
                }

                /* ===== ADMINISTRASI PENYIDIKAN ===== */
                foreach ($data['admin_documents'] ?? [] as $i => $doc) {

                    $journeys[] = $this->createJourney(
                        $report,
                        ReportJourneyType::INVESTIGATION,
                        [
                            'text' => 'Administrasi penyidikan: ' . ($doc['name'] ?? ''),
                            'doc_kind' => 'penyidikan',
                            'doc_number' => $doc['number'] ?? null,
                            'doc_date' => $doc['date'] ?? null,
                        ],
                        isset($files['admin_files'][$i]) ? [$files['admin_files'][$i]] : [],
                        $divisionId,
                        $institutionId
                    );
                }

                $hasInvest = !empty($journeys) || $canSkipInspection;

                /* ===== SIDANG ===== */
                $needsTrial = (
                    !empty($data['trial_doc_number'])
                    || !empty($data['trial_decision'])
                    || !empty($files['trial_file'])
                    || $action === 'complete'
                );

                if ($needsTrial) {
                    if (!$hasInvest) {
                        throw new RuntimeException('Tambahkan dokumen penyidikan sebelum sidang.');
                    }

                    $journeys[] = $this->createJourney(
                        $report,
                        ReportJourneyType::TRIAL,
                        [
                            'text' => 'Dokumen sidang',
                            'doc_kind' => 'sidang',
                            'doc_number' => $data['trial_doc_number'] ?? null,
                            'doc_date' => $data['trial_doc_date'] ?? null,
                            'decision' => $data['trial_decision'] ?? null,
                        ],
                        isset($files['trial_file']) && $files['trial_file']
                            ? [$files['trial_file']]
                            : [],
                        $divisionId,
                        $institutionId
                    );
                }

                /* ===== COMPLETE ===== */
                if ($action === 'complete') {

                    $journeys[] = $this->createJourney(
                        $report,
                        ReportJourneyType::COMPLETED,
                        [
                            'text' => 'Laporan selesai',
                            'doc_kind' => 'sidang',
                            'decision' => $data['trial_decision'] ?? null,
                        ],
                        [],
                        $divisionId,
                        $institutionId
                    );

                    $this->finishAccess($report);
                }
            }

            if (empty($journeys)) {
                throw new RuntimeException('Tidak ada progress yang disimpan.');
            }

            $this->notificationService->notifyReportStatus(
                $report,
                $journeys[array_key_last($journeys)]->type
            );

            DB::commit();

            return [
                'status' => true,
                'message' => 'Progress laporan berhasil diperbarui.',
                'data' => $journeys,
            ];

        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
}


    private function determineFirstFlow(Report $report): string
    {
        $creatorDivision = $report->creator?->division;

        if ($creatorDivision?->canInspection()) {
            return 'inspection';
        }

        if ($creatorDivision?->canInvestigation()) {
            return 'investigation';
        }

        return 'inspection';
    }

    private function lastJourneyType(Report $report): ReportJourneyType
    {
        $lastJourney = $report->journeys()->latest()->first();

        return $lastJourney ? $lastJourney->typeEnum() : ReportJourneyType::SUBMITTED;
    }

    private function createJourney(
        Report $report,
        ReportJourneyType $type,
        array $description,
        array $files = [],
        ?int $divisionId = null,
        ?int $institutionId = null,
        bool $touchReportStatus = true,
    ): ReportJourney {
        $journeyData = [
            'report_id' => $report->id,
            'institution_id' => $institutionId,
            'division_id' => $divisionId,
            'type' => $type->value,
            'description' => $description,
        ];

        /** @var ReportJourney $journey */
        $journey = $this->repository->store($journeyData);

        $createdFiles = [];

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $original = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $file->getClientOriginalName());
            $filename = time() . '-' . $original;
            $storedPath = $file->storeAs('evidences', $filename, 'public');

            ReportEvidence::create([
                'report_journey_id' => $journey->id,
                'report_id' => $journey->report_id,
                'file_url' => Storage::url($storedPath),
                'file_type' => strtolower($file->getClientOriginalExtension()),
            ]);
            $createdFiles[] = $filename;
        }

        if ($touchReportStatus) {
            $updateData = ['status' => $type->value];

            if ($type === ReportJourneyType::COMPLETED) {
                $updateData['finish_time'] = Carbon::now();
            }

            $report->update($updateData);
        }

        if (!empty($createdFiles)) {
            $this->notificationService->notifyBuktiDitambahkan(
                $journey->report,
                implode(', ', $createdFiles)
            );
        }

        return $journey;
    }

    private function handleTransferAccess(Report $report, ?int $currentDivisionId, ?int $targetDivisionId): void
    {
        // close only current division access
        AccessData::where('report_id', $report->id)
            ->where('division_id', $currentDivisionId)
            ->update(['is_finish' => true]);

        // give new division access
        if ($targetDivisionId) {
            AccessData::firstOrCreate([
                'report_id' => $report->id,
                'division_id' => $targetDivisionId,
            ], [
                'is_finish' => false,
            ]);
        }
    }


    private function finishAccess(Report $report): void
    {
        AccessData::where('report_id', $report->id)
            ->update(['is_finish' => true]);
    }

    private function normalizeType(null|ReportJourneyType|string $type): ?ReportJourneyType
    {
        if ($type instanceof ReportJourneyType) {
            return $type;
        }

        if (!is_string($type) || $type === '') {
            return null;
        }

        $enum = ReportJourneyType::tryFrom($type);

        if ($enum) {
            return $enum;
        }

        $upper = strtoupper($type);
        $enum = ReportJourneyType::tryFrom($upper);

        if ($enum) {
            return $enum;
        }

        foreach (ReportJourneyType::cases() as $case) {
            if (strcasecmp($case->label(), $type) === 0) {
                return $case;
            }
        }

        return null;
    }
}

