<?php

namespace App\Services;

use App\Enums\ReportJourneyType;
use App\Interfaces\ReportJourneyRepositoryInterface;
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
        protected ReportJourneyRepositoryInterface $repository
    ) {
    }

    public function paginateByReport(int $reportId, int $perPage = 5, string $order = 'desc'): LengthAwarePaginator
    {
        $raw = $this->repository->paginateByReport($reportId, $perPage, $order);

        // FIX 1: Normalisasi ke paginator
        if ($raw instanceof \Illuminate\Pagination\LengthAwarePaginator ||
            $raw instanceof \Illuminate\Pagination\Paginator) {

            $paginator = $raw;
            $journeys = collect($paginator->items());

        } elseif ($raw instanceof \Illuminate\Support\Collection) {

            // REPOSITORY RETURN COLLECTION (INI YANG TERJADI DI KASUS LU)
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

        // FIX 2: collect unique ids
        $institutionIds = $journeys->pluck('target_institution_id')->filter()->unique();
        $divisionIds    = $journeys->pluck('target_division_id')->filter()->unique();

        $institutions = Institution::whereIn('id', $institutionIds)->get()->keyBy('id');
        $divisions    = Division::whereIn('id', $divisionIds)->get()->keyBy('id');

        // FIX 3: transform journeys
        $journeys = $journeys->map(function ($journey) use ($institutions, $divisions) {
            $journey->target_institution = $journey->target_institution_id
                ? $institutions[$journey->target_institution_id] ?? null
                : null;

            $journey->target_division = $journey->target_division_id
                ? $divisions[$journey->target_division_id] ?? null
                : null;

            return $journey;
        });

        // FIX 4: assign back WITHOUT setCollection() kalau tidak ada
        if (method_exists($paginator, 'setCollection')) {
            $paginator->setCollection($journeys);
        } else {
            // bikin paginator baru (fallback)
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

            if (! $type || $reportId <= 0) {
                throw new \InvalidArgumentException('Data tahapan tidak lengkap.');
            }

            $journeyData = [
                'report_id' => $reportId,
                'institution_id' => $data['institution_id'] ?? null,
                'division_id' => $data['division_id'] ?? null,
                'type' => $type->value,
                'description' => [
                    'text' => $data['description'] ?? '',
                    'institution_target_id' => $data['institution_target_id'] ?? null,
                    'division_target_id' => $data['division_target_id'] ?? null,
                ],
            ];

            /** @var ReportJourney $journey */
            $journey = $this->repository->store($journeyData);

            foreach ($files as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $storedPath = $file->store('evidences', 'public');

                ReportEvidence::create([
                    'report_journey_id' => $journey->id,
                    'report_id' => $journey->report_id,
                    'file_url' => Storage::url($storedPath),
                    'file_type' => strtolower((string) $file->getClientOriginalExtension()),
                ]);
            }

            $reportUpdate = ['status' => $type->value];

            if ($type === ReportJourneyType::COMPLETED) {
                $reportUpdate['finish_time'] = now();
            }

            Report::whereKey($journey->report_id)->update($reportUpdate);

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
                'message' => 'Gagal menambahkan tahapan penanganan.',
            ];
        }
    }

    private function normalizeType(null|ReportJourneyType|string $type): ?ReportJourneyType
    {
        if ($type instanceof ReportJourneyType) {
            return $type;
        }

        if (! is_string($type) || $type === '') {
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
