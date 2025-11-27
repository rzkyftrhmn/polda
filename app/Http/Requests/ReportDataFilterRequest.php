<?php

namespace App\Http\Requests;

use App\Enums\ReportJourneyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportDataFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statusValues = array_map(static fn (ReportJourneyType $type) => $type->value, ReportJourneyType::cases());

        return [
            'q' => ['nullable', 'string'],
            'status' => ['nullable', 'string', Rule::in($statusValues)],
            'category_id' => ['nullable', 'integer', 'exists:report_categories,id'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'division_id' => ['nullable', 'integer', 'exists:divisions,id'],
            'incident_from' => ['nullable', 'date'],
            'incident_to' => ['nullable', 'date'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
            'finish_from' => ['nullable', 'date'],
            'finish_to' => ['nullable', 'date'],
            'sort_by' => ['nullable', 'string', Rule::in(['created_at', 'incident_datetime', 'finish_time', 'status', 'code', 'title'])],
            'sort_dir' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    public function filters(): array
    {
        return [
            'q' => $this->filled('q') ? trim((string) $this->input('q')) : null,
            'status' => $this->filled('status') ? (string) $this->input('status') : null,
            'category_id' => $this->filled('category_id') ? (int) $this->input('category_id') : null,
            'province_id' => $this->filled('province_id') ? (int) $this->input('province_id') : null,
            'division_id' => $this->filled('division_id') ? (int) $this->input('division_id') : null,
            'incident_from' => $this->filled('incident_from') ? (string) $this->input('incident_from') : null,
            'incident_to' => $this->filled('incident_to') ? (string) $this->input('incident_to') : null,
            'created_from' => $this->filled('created_from') ? (string) $this->input('created_from') : null,
            'created_to' => $this->filled('created_to') ? (string) $this->input('created_to') : null,
            'finish_from' => $this->filled('finish_from') ? (string) $this->input('finish_from') : null,
            'finish_to' => $this->filled('finish_to') ? (string) $this->input('finish_to') : null,
            'sort_by' => $this->filled('sort_by') ? (string) $this->input('sort_by') : null,
            'sort_dir' => $this->filled('sort_dir') ? strtolower((string) $this->input('sort_dir')) : null,
        ];
    }
}
