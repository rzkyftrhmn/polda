<?php

namespace App\Repositories;

use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ReportDataRepository
{
    public function baseQuery(): Builder
    {
        return Report::query()
            ->with([
                'category',
                'province',
                'city',
                'district',
            ]);
    }

    public function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['q'])) {
            $keyword = $filters['q'];
            $query->where(function (Builder $builder) use ($keyword): void {
                $builder->where('code', 'like', "%{$keyword}%")
                    ->orWhere('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('name_of_reporter', 'like', "%{$keyword}%")
                    ->orWhereHas('suspects', function (Builder $q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['division_id'])) {
            $query->where('division_id', $filters['division_id']);
        }

        $this->applyDateFilter($query, 'incident_datetime', $filters['incident_from'] ?? null, $filters['incident_to'] ?? null);
        $this->applyDateFilter($query, 'created_at', $filters['created_from'] ?? null, $filters['created_to'] ?? null);
        $this->applyFinishTimeFilter($query, $filters['finish_from'] ?? null, $filters['finish_to'] ?? null);

        return $query;
    }

    public function applySorting(Builder $query, array $filters): Builder
    {
        $allowedSorts = ['created_at', 'incident_datetime', 'finish_time', 'status', 'code', 'title'];
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = strtolower($filters['sort_dir'] ?? 'desc');

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = 'desc';
        }

        return $query->orderBy($sortBy, $sortDir);
    }

    protected function applyDateFilter(Builder $query, string $column, ?string $from, ?string $to): void
    {
        $fromDate = $from ? Carbon::parse($from)->startOfDay() : null;
        $toDate = $to ? Carbon::parse($to)->endOfDay() : null;

        if ($fromDate && $toDate) {
            $query->whereBetween($column, [$fromDate, $toDate]);
            return;
        }

        if ($fromDate) {
            $query->where($column, '>=', $fromDate);
            return;
        }

        if ($toDate) {
            $query->where($column, '<=', $toDate);
        }
    }

    protected function applyFinishTimeFilter(Builder $query, ?string $from, ?string $to): void
    {
        $fromTimestamp = $from ? Carbon::parse($from)->startOfDay()->getTimestamp() : null;
        $toTimestamp = $to ? Carbon::parse($to)->endOfDay()->getTimestamp() : null;

        if ($fromTimestamp && $toTimestamp) {
            $query->whereBetween('finish_time', [$fromTimestamp, $toTimestamp]);
            return;
        }

        if ($fromTimestamp) {
            $query->where('finish_time', '>=', $fromTimestamp);
            return;
        }

        if ($toTimestamp) {
            $query->where('finish_time', '<=', $toTimestamp);
        }
    }
}
