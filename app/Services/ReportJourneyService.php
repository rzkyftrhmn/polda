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
        $paginator = $this->repository->paginateByReport($reportId, $perPage, $order);

        $journeys = $paginator->getCollection();

        $institutionIds = $journeys
            ->pluck('target_institution_id')
            ->filter()
            ->unique()
            ->values();

        $divisionIds = $journeys
            ->pluck('target_division_id')
            ->filter()
            ->unique()
            ->values();

        $institutions = Institution::whereIn('id', $institutionIds)->get()->keyBy('id');
        $divisions = Division::whereIn('id', $divisionIds)->get()->keyBy('id');

        $journeys->transform(static function (ReportJourney $journey) use ($institutions, $divisions): ReportJourney {
            $journey->target_institution = $journey->target_institution_id
                ? $institutions->get($journey->target_institution_id)
                : null;
            $journey->target_division = $journey->target_division_id
                ? $divisions->get($journey->target_division_id)
                : null;

            return $journey;
        });

        return $paginator->setCollection($journeys);
    }

    public function store(array $data, array $files = []): array
    {
        DB::beginTransaction();

        try {
            $type = ReportJourneyType::from($data['type']);

            $journeyData = [
                'report_id' => $data['report_id'],
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

            if ($type !== ReportJourneyType::SUBMITTED) {
                Report::whereKey($journey->report_id)->update(['status' => $type->value]);
            }

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
}
