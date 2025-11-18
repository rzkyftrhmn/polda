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
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            throw new \RuntimeException("paginateByReport must return paginator or collection");
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

    public function store(array $data, array $files = []): array
    {
        DB::beginTransaction();

        try {
            $type = $this->normalizeType($data['type'] ?? null);
            $reportId = (int) ($data['report_id'] ?? 0);

            if (!$type || $reportId <= 0) {
                throw new \InvalidArgumentException('Data tahapan tidak lengkap.');
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
                $this->finishAccess($report, $data['division_id'] ?? null);
            }

            $this->notificationService->notifyReportStatus($journey->report, $type->value);

            DB::commit();

            return [
                'status' => true,
                'message' => 'Tahapan penanganan berhasil ditambahkan.',
                'data' => $journey->load('evidences'),
            ];
        } catch (Throwable $throwable) {
            DB::rollBack();

            report($throwable);

            return [
                'status' => false,
                'message' => 'Gagal menambahkan tahapan penanganan....',
            ];
        }
    }

    public function storeProgress(Report $report, array $data, array $files, ?int $divisionId = null, ?int $institutionId = null): array
    {
        DB::beginTransaction();

        try {
            $lastJourney = $report->journeys()->latest()->first();
            $lastType = $lastJourney ? $lastJourney->typeEnum() : ReportJourneyType::SUBMITTED;
            $journeys = [];

            // administrasi penyidikan
            foreach ($data['admin_documents'] ?? [] as $index => $doc) {
                $file = $files['admin_files'][$index] ?? null;
                $type = ReportJourneyType::INVESTIGATION;

                if (!$this->validFlow($lastType, $type)) {
                    throw new \RuntimeException('Urutan tahapan tidak valid untuk Administrasi Penyidikan.');
                }

                $journeys[] = $this->createJourney(
                    $report,
                    $type,
                    [
                        'text' => 'Administrasi penyidikan: ' . ($doc['name'] ?? ''),
                        'doc_kind' => 'penyidikan',
                        'doc_number' => $doc['number'] ?? null,
                        'doc_date' => $doc['date'] ?? null,
                    ],
                    $file ? [$file] : [],
                    $divisionId,
                    $institutionId
                );

                $lastType = $type;
            }

            // dokumen sidang
            if (!empty($data['trial_doc_number']) || !empty($files['trial_file'])) {
                $type = ReportJourneyType::TRIAL;

                if (!$this->validFlow($lastType, $type)) {
                    throw new \RuntimeException('Tidak bisa SIDANG karena status terakhir belum LIMPAH.');
                }

                $journeys[] = $this->createJourney(
                    $report,
                    $type,
                    [
                        'text' => 'Dokumen sidang',
                        'doc_kind' => 'sidang',
                        'doc_number' => $data['trial_doc_number'] ?? null,
                        'doc_date' => $data['trial_doc_date'] ?? null,
                        'decision' => $data['trial_decision'] ?? null,
                    ],
                    isset($files['trial_file']) && $files['trial_file'] ? [$files['trial_file']] : [],
                    $divisionId,
                    $institutionId
                );

                $lastType = $type;
            }

            // aksi akhir
            $actionType = $data['action'] === 'transfer'
                ? ReportJourneyType::TRANSFER
                : ReportJourneyType::COMPLETED;

            if (!$this->validFlow($lastType, $actionType)) {
                $messages = [
                    ReportJourneyType::TRANSFER->value => 'Tidak bisa LIMPAH karena status terakhir belum PENYELIDIKAN.',
                    ReportJourneyType::TRIAL->value => 'Tidak bisa SIDANG karena status terakhir belum LIMPAH.',
                    ReportJourneyType::INVESTIGATION->value => 'Tidak bisa PENYELIDIKAN jika laporan sudah SELESAI.',
                    ReportJourneyType::COMPLETED->value => 'Laporan sudah SELESAI sebelumnya.',
                ];

                throw new \RuntimeException($messages[$actionType->value] ?? 'Urutan tahapan tidak valid.');
            }

            $docKind = $this->detectDocKind($data);
            $journey = $this->createJourney(
                $report,
                $actionType,
                [
                    'text' => $actionType === ReportJourneyType::TRANSFER ? 'Laporan dilimpahkan' : 'Laporan selesai',
                    'doc_kind' => $docKind,
                    'doc_number' => $data['inspection_doc_number'] ?? null,
                    'doc_date' => $data['inspection_doc_date'] ?? null,
                    'conclusion' => $data['inspection_conclusion'] ?? null,
                    'decision' => $actionType === ReportJourneyType::TRANSFER ? 'LIMPAH' : 'SELESAI',
                    'institution_target_id' => $data['target_institution_id'] ?? null,
                    'division_target_id' => $data['target_division_id'] ?? null,
                ],
                $files['inspection_files'] ?? [],
                $divisionId,
                $institutionId
            );

            $journeys[] = $journey;

            if ($actionType === ReportJourneyType::TRANSFER) {
                $this->handleTransferAccess($report, $divisionId, $data['target_division_id'] ?? null);
            } else {
                $this->finishAccess($report, $divisionId);
            }

            $this->notificationService->notifyReportStatus($report, $actionType->value);

            DB::commit();

            return [
                'status' => true,
                'message' => 'Progress laporan berhasil diperbarui.',
                'data' => $journeys,
            ];
        } catch (Throwable $throwable) {
            DB::rollBack();
            report($throwable);

            return [
                'status' => false,
                'message' => $throwable->getMessage(),
            ];
        }
    }

    private function createJourney(
        Report $report,
        ReportJourneyType $type,
        array $description,
        array $files = [],
        ?int $divisionId = null,
        ?int $institutionId = null
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

        $report->update([
            'status' => $type->value,
            'finish_time' => $type === ReportJourneyType::COMPLETED ? now() : $report->finish_time,
        ]);

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
        if ($currentDivisionId) {
            AccessData::where('report_id', $report->id)
                ->where('division_id', $currentDivisionId)
                ->where('is_finish', false)
                ->update(['is_finish' => true]);
        }

        if ($targetDivisionId) {
            AccessData::create([
                'report_id' => $report->id,
                'division_id' => $targetDivisionId,
                'is_finish' => false,
            ]);
        }
    }

    private function finishAccess(Report $report, ?int $divisionId = null): void
    {
        if ($divisionId) {
            AccessData::where('report_id', $report->id)
                ->where('division_id', $divisionId)
                ->update(['is_finish' => true]);
        }
    }

    private function detectDocKind(array $data): string
    {
        if (!empty($data['inspection_doc_number']) || !empty($data['inspection_doc_date'])) {
            return 'pemeriksaan';
        }

        if (!empty($data['admin_documents'])) {
            return 'penyidikan';
        }

        if (!empty($data['trial_doc_number'])) {
            return 'sidang';
        }

        return 'lainnya';
    }

    private function validFlow(ReportJourneyType $lastType, ReportJourneyType $currentType): bool
    {
        return match ($currentType) {
            ReportJourneyType::INVESTIGATION => $lastType !== ReportJourneyType::COMPLETED,
            ReportJourneyType::TRANSFER => $lastType === ReportJourneyType::INVESTIGATION,
            ReportJourneyType::TRIAL => $lastType === ReportJourneyType::TRANSFER,
            ReportJourneyType::COMPLETED => $lastType !== ReportJourneyType::COMPLETED,
            default => false,
        };
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

