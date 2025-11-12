<?php

namespace App\Enums;

enum ReportJourneyType: string
{
    case SUBMITTED = 'SUBMITTED';
    case INVESTIGATION = 'PEMERIKSAAN';
    case TRANSFER = 'LIMPAH';
    case TRIAL = 'SIDANG';
    case COMPLETED = 'SELESAI';

    public function label(): string
    {
        return match ($this) {
            self::SUBMITTED => 'SUBMITTED',
            self::INVESTIGATION => 'PENYELIDIKAN',
            self::TRANSFER => 'LIMPAH',
            self::TRIAL => 'SIDANG',
            self::COMPLETED => 'SELESAI',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::SUBMITTED => 'bg-secondary',
            self::INVESTIGATION => 'bg-info',
            self::TRANSFER => 'bg-warning text-dark',
            self::TRIAL => 'bg-primary',
            self::COMPLETED => 'bg-success',
        };
    }

    public function isManual(): bool
    {
        return $this !== self::SUBMITTED;
    }

    /**
     * @return array<int, self>
     */
    public static function manualOptions(): array
    {
        return array_values(array_filter(self::cases(), static fn (self $type) => $type->isManual()));
    }
}
