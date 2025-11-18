<?php

namespace App\Models;

use App\Enums\ReportJourneyType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_id',
        'code',
        'title',
        'incident_datetime',
        'province_id',
        'city_id',
        'district_id',
        'address_detail',
        'category_id',
        'status',
        'description',
        'finish_time',
        'name_of_reporter',
        'phone_of_reporter',
        'address_of_reporter',
        'created_by',
    ];

    protected $casts = [
        'incident_datetime' => 'datetime',
    ];

    protected function finishTime(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) {
                    return null;
                }

                if (is_numeric($value)) {
                    return Carbon::createFromTimestamp((int) $value);
                }

                return Carbon::parse($value);
            },
            set: fn ($value) => $value ? Carbon::parse($value)->timestamp : null,
        );
    }

    public function journeys(): HasMany
    {
        return $this->hasMany(ReportJourney::class, 'report_id');
    }

    public function accessDatas()
    {
        return $this->hasMany(AccessData::class, 'report_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ReportCategory::class, 'category_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function reportCategory()
    {
        return $this->belongsTo(ReportCategory::class, 'category_id');
    }

    public function suspects()
    {
        return $this->hasMany(Suspect::class, 'report_id');
    }

    public function evidences()
    {
        return $this->hasMany(ReportEvidence::class, 'report_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by'); 
    }

    public function divisi()
    {
        return $this->belongsTo(Division::class, 'division_id'); 
    }
}
