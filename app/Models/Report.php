<?php

namespace App\Models;

use App\Enums\ReportJourneyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'incident_datetime',
        'province_id',
        'city_id',
        'district_id',
        'address_detail',
        'category_id',
        'status',
        'code',
        'finish_time',
    ];

    protected $casts = [
        'incident_datetime' => 'datetime',
        'finish_time' => 'datetime',
    ];

    public function journeys()
    {
        return $this->hasMany(ReportJourney::class, 'report_id');
    }

    public function category()
    {
        return $this->belongsTo(ReportCategory::class, 'category_id');
    }

    protected static function booted(): void
    {
        static::created(function (Report $report): void {
            if ($report->journeys()->exists()) {
                return;
            }

            $report->journeys()->create([
                'type' => ReportJourneyType::SUBMITTED->value,
                'description' => [
                    'text' => 'Laporan diterima oleh sistem.',
                ],
            ]);
        });
    }
}
