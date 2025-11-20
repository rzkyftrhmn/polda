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
use Illuminate\Support\Facades\Schema;
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

    public function findJourneyByDocNumber(
        Report $report,
        ReportJourneyType $type,
        ?string $docNumber,
        ?string $docKind = null
    ): ?ReportJourney {
        if (!$docNumber) {
            return null;
        }

        $needleNumber = trim((string) $docNumber);

        return $report->journeys()
            ->where('type', $type->value)
            ->latest('id')
            ->get()
            ->first(function ($journey) use ($needleNumber, $docKind) {
                $payload = $journey->description_payload ?? [];
                $matchesNumber = isset($payload['doc_number']) &&
                    strcasecmp(trim((string) $payload['doc_number']), $needleNumber) === 0;
                $matchesKind = $docKind === null ||
                    (($payload['doc_kind'] ?? null) === $docKind);

                return $matchesNumber && $matchesKind;
            });
    }

    public function journeyHasEvidence(
        Report $report,
        ReportJourneyType $type,
        ?string $docNumber,
        ?string $docKind = null
    ): bool {
        $journey = $this->findJourneyByDocNumber($report, $type, $docNumber, $docKind);

        return $journey ? $journey->evidences()->exists() : false;
    }

    public function latestInspectionPrefill(Report $report): array
    {
        $journeys = $report->journeys()
            ->where('type', ReportJourneyType::INVESTIGATION->value)
            ->latest('id')
            ->get();

        $journey = $journeys->first(fn ($j) => ($j->description_payload['doc_kind'] ?? null) === 'pemeriksaan')
            ?? $journeys->first(); // fallback data lama tanpa doc_kind

        if (!$journey) {
            return [];
        }

        $payload = $journey->description_payload ?? [];
        $docDate = $payload['doc_date'] ?? null;

        if ($docDate instanceof \DateTimeInterface) {
            $docDate = $docDate->format('Y-m-d');
        }

        return [
            'doc_number' => $payload['doc_number'] ?? null,
            'doc_date' => $docDate,
            'conclusion' => $payload['conclusion'] ?? null,
        ];
    }

    public function adminDocumentsPrefill(Report $report): array
    {
        $journeys = $report->journeys()
            ->where('type', ReportJourneyType::INVESTIGATION->value)
            ->with('evidences')
            ->orderBy('id')
            ->get()
            ->filter(function ($journey) {
                $kind = $journey->description_payload['doc_kind'] ?? null;
                $text = strtolower($journey->description ?? '');

                if ($kind === 'penyidikan') {
                    return true;
                }

                // data lama tanpa doc_kind tapi teks berisi administrasi penyidikan
                return $kind === null && str_contains($text, 'administrasi penyidikan');
            });

        return $journeys->values()->map(function (ReportJourney $journey) {
            $payload = $journey->description_payload ?? [];
            $evidence = $journey->evidences->first();
            $docName = $this->extractDocNameFromText($journey->description ?? '');
            $docDate = $payload['doc_date'] ?? null;

            if ($docDate instanceof \DateTimeInterface) {
                $docDate = $docDate->format('Y-m-d');
            }

            return [
                'id' => $journey->id,
                'name' => $docName,
                'number' => $payload['doc_number'] ?? null,
                'date' => $docDate,
                'file_url' => $evidence->file_url ?? null,
                'file_name' => $evidence ? basename($evidence->file_url) : null,
            ];
        })->all();
    }

    private function extractDocNameFromText(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        $parts = explode(':', $text, 2);

        if (count($parts) === 2) {
            return trim($parts[1]);
        }

        return trim($text);
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
                    $data['division_id'] ?? null
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
            $hasExistingInvestigation = $report->journeys()
                ->where('type', ReportJourneyType::INVESTIGATION->value)
                ->exists();

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

                $inspectionDocNumber = $data['inspection_doc_number'] ?? null;
                $inspectionFiles = $files['inspection_files'] ?? [];

                // Upsert Investigation journey by report + doc number + doc kind
                $journeys[] = $this->upsertJourneyWithFiles(
                    $report,
                    ReportJourneyType::INVESTIGATION,
                    [
                        'text' => 'Dokumen pemeriksaan',
                        'doc_kind' => 'pemeriksaan',
                        'doc_number' => $inspectionDocNumber,
                        'doc_date' => $data['inspection_doc_date'] ?? null,
                        'conclusion' => $data['inspection_conclusion'] ?? null,
                    ],
                    $inspectionFiles,
                    $divisionId,
                    $institutionId,
                    $touchStatus,
                    'pemeriksaan',
                    $inspectionDocNumber
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
                        $divisionId
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
                $adminDocNumbers = [];
                foreach ($data['admin_documents'] ?? [] as $i => $doc) {
                    $docNumber = $doc['number'] ?? null;

                    if ($docNumber && isset($adminDocNumbers[$docNumber])) {
                        continue; // hindari duplikasi pada request yang sama
                    }

                    if ($docNumber) {
                        $adminDocNumbers[$docNumber] = true;
                    }

                    $journeys[] = $this->upsertJourneyWithFiles(
                        $report,
                        ReportJourneyType::INVESTIGATION,
                        [
                            'text' => 'Administrasi penyidikan: ' . ($doc['name'] ?? ''),
                            'doc_kind' => 'penyidikan',
                            'doc_number' => $docNumber,
                            'doc_date' => $doc['date'] ?? null,
                        ],
                        isset($files['admin_files'][$i]) ? [$files['admin_files'][$i]] : [],
                        $divisionId,
                        $institutionId,
                        true,
                        'penyidikan',
                        $docNumber
                    );
                }

                $hasInvest = $hasExistingInvestigation || !empty($journeys) || $canSkipInspection;

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

                    $journeys[] = $this->upsertJourneyWithFiles(
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
                        $institutionId,
                        true,
                        'sidang',
                        $data['trial_doc_number'] ?? null
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

    private function persistEvidenceFiles(ReportJourney $journey, array $files, bool $replaceExisting = false): array
    {
        $uploaded = array_filter($files, static fn ($file) => $file instanceof UploadedFile);

        if ($replaceExisting) {
            foreach ($journey->evidences as $evidence) {
                $this->deleteEvidenceFile($evidence);
                $evidence->delete();
            }
        }

        if (empty($uploaded)) {
            return [];
        }

        $createdFiles = [];

        foreach ($uploaded as $file) {
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

        return $createdFiles;
    }

    private function deleteEvidenceFile(ReportEvidence $evidence): void
    {
        $fileUrl = $evidence->file_url ?? '';

        $path = null;
        if (str_starts_with($fileUrl, '/storage/')) {
            $path = substr($fileUrl, 9);
        } elseif (str_starts_with($fileUrl, 'storage/')) {
            $path = substr($fileUrl, 8);
        }

        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function upsertJourneyWithFiles(
        Report $report,
        ReportJourneyType $type,
        array $description,
        array $files = [],
        ?int $divisionId = null,
        ?int $institutionId = null,
        bool $touchReportStatus = true,
        ?string $docKindForLookup = null,
        ?string $docNumberForLookup = null
    ): ReportJourney {
        $lookupKind = $docKindForLookup ?? ($description['doc_kind'] ?? null);
        $lookupNumber = $docNumberForLookup ?? ($description['doc_number'] ?? null);

        $existingJourney = $this->findJourneyByDocNumber(
            $report,
            $type,
            $lookupNumber,
            $lookupKind
        );

        $shouldReplaceEvidence = !empty(array_filter(
            $files,
            static fn ($file) => $file instanceof UploadedFile
        ));

        if ($existingJourney) {
            $existingJourney->division_id = $divisionId;
            $existingJourney->type = $type->value;
            $existingJourney->description = $description;
            $existingJourney->save();

            $createdFiles = $this->persistEvidenceFiles($existingJourney, $files, $shouldReplaceEvidence);

            if ($touchReportStatus) {
                $updateData = ['status' => $type->value];

                if ($type === ReportJourneyType::COMPLETED) {
                    $updateData['finish_time'] = $this->resolveFinishTimeValue();
                }

                $this->applyReportUpdate($report, $updateData);
            }

            if (!empty($createdFiles)) {
                $this->notificationService->notifyBuktiDitambahkan(
                    $existingJourney->report,
                    implode(', ', $createdFiles)
                );
            }

            return $existingJourney->refresh();
        }

        return $this->createJourney(
            $report,
            $type,
            $description,
            $files,
            $divisionId,
            $institutionId,
            $touchReportStatus
        );
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
            'division_id' => $divisionId,
            'type' => $type->value,
            'description' => $description,
        ];

        /** @var ReportJourney $journey */
        $journey = $this->repository->store($journeyData);

        $createdFiles = $this->persistEvidenceFiles($journey, $files);

        if ($touchReportStatus) {
            $updateData = ['status' => $type->value];

            if ($type === ReportJourneyType::COMPLETED) {
                $updateData['finish_time'] = $this->resolveFinishTimeValue();
            }

            $this->applyReportUpdate($report, $updateData);
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

    public function latestInspectionEvidence(Report $report): array
    {
        return $this->latestEvidenceByKind($report, ReportJourneyType::INVESTIGATION, 'pemeriksaan');
    }

    public function latestTrialPrefill(Report $report): array
    {
        $journey = $report->journeys()
            ->where('type', ReportJourneyType::TRIAL->value)
            ->latest('id')
            ->get()
            ->first(function ($j) {
                $kind = $j->description_payload['doc_kind'] ?? null;

                return $kind === 'sidang' || $kind === null;
            });

        if (!$journey) {
            return [];
        }

        $payload = $journey->description_payload ?? [];
        $docDate = $payload['doc_date'] ?? null;

        if ($docDate instanceof \DateTimeInterface) {
            $docDate = $docDate->format('Y-m-d');
        }

        return [
            'doc_number' => $payload['doc_number'] ?? null,
            'doc_date' => $docDate,
            'decision' => $payload['decision'] ?? null,
        ];
    }

    public function latestTrialEvidence(Report $report): array
    {
        return $this->latestEvidenceByKind($report, ReportJourneyType::TRIAL, 'sidang');
    }

    private function latestEvidenceByKind(
        Report $report,
        ReportJourneyType $type,
        ?string $docKind
    ): array
    {
        $journey = $report->journeys()
            ->where('type', $type->value)
            ->latest('id')
            ->with('evidences')
            ->get()
            ->first(function ($journey) use ($docKind) {
                $kind = $journey->description_payload['doc_kind'] ?? null;

                // terima doc_kind yang cocok, atau kosong (data lama)
                return ($docKind === null) || $kind === $docKind || $kind === null;
            });

        if (!$journey) {
            return [];
        }

        return $journey->evidences
            ->map(function ($evidence) {
                return [
                    'url' => $evidence->file_url ?? null,
                    'name' => $evidence->file_url ? basename($evidence->file_url) : 'Lampiran',
                    'type' => $evidence->file_type ?? null,
                ];
            })
            ->values()
            ->all();
    }

    private function applyReportUpdate(Report $report, array $updateData): void
    {
        $columnType = $this->determineFinishTimeColumnType();
        $needsRawUpdate = isset($updateData['finish_time']) &&
            (!$columnType || !$this->isDateTimeType($columnType));

        if ($needsRawUpdate) {
            $report->newQuery()->whereKey($report->id)->update($updateData);
            $report->refresh(); // keep the current instance in sync

            return;
        }

        $report->update($updateData);
    }

    private function resolveFinishTimeValue(): int|Carbon
    {
        $columnType = $this->determineFinishTimeColumnType();

        if ($columnType && $this->isDateTimeType($columnType)) {
            return Carbon::now();
        }

        return Carbon::now()->getTimestamp();
    }

    private function determineFinishTimeColumnType(): ?string
    {
        try {
            return Schema::getColumnType('reports', 'finish_time');
        } catch (Throwable) {
            return $this->determineFinishTimeColumnTypeFallback();
        }
    }

    private function determineFinishTimeColumnTypeFallback(): ?string
    {
        try {
            $connection = Schema::getConnection();
            $driver     = $connection->getDriverName();

            if ($driver === 'mysql') {
                $result = $connection->selectOne('SHOW COLUMNS FROM `reports` LIKE ?', ['finish_time']);

                if ($result && isset($result->Type)) {
                    return (string) $result->Type;
                }
            }
        } catch (Throwable) {
            return null;
        }

        return null;
    }

    private function isDateTimeType(string $type): bool
    {
        $type = strtolower($type);

        return str_contains($type, 'date') ||
            str_contains($type, 'time');
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
