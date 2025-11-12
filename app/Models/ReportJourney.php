<?php

namespace App\Models;

use App\Enums\ReportJourneyType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportJourney extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'institution_id',
        'division_id',
        'type',
        'description',
    ];

    protected $appends = [
        'target_institution_id',
        'target_division_id',
        'type_label',
        'badge_class',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    public function evidences()
    {
        return $this->hasMany(ReportEvidence::class, 'report_journey_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->descriptionPayload($value)['text'] ?? '',
            set: function ($value) {
                if (is_array($value)) {
                    return json_encode($this->mergeDescriptionPayload($value));
                }

                return json_encode($this->mergeDescriptionPayload(['text' => (string) $value]));
            }
        );
    }

    public function getDescriptionPayloadAttribute(): array
    {
        return $this->descriptionPayload($this->attributes['description'] ?? null);
    }

    public function getTargetInstitutionIdAttribute(): ?int
    {
        $payload = $this->description_payload;

        $id = $payload['institution_target_id'] ?? null;

        return $id !== null ? (int) $id : null;
    }

    public function getTargetDivisionIdAttribute(): ?int
    {
        $payload = $this->description_payload;

        $id = $payload['division_target_id'] ?? null;

        return $id !== null ? (int) $id : null;
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->typeEnum()->label();
    }

    public function getBadgeClassAttribute(): string
    {
        return $this->typeEnum()->badgeClass();
    }

    public function typeEnum(): ReportJourneyType
    {
        return ReportJourneyType::from($this->type);
    }

    private function descriptionPayload(?string $value): array
    {
        if ($value === null) {
            return $this->defaultDescriptionPayload();
        }

        $decoded = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $this->mergeDescriptionPayload($decoded);
        }

        return $this->defaultDescriptionPayload(['text' => $value]);
    }

    private function mergeDescriptionPayload(array $payload): array
    {
        return array_merge($this->defaultDescriptionPayload(), $payload);
    }

    private function defaultDescriptionPayload(array $overrides = []): array
    {
        return array_merge([
            'text' => '',
            'institution_target_id' => null,
            'division_target_id' => null,
        ], $overrides);
    }
}
