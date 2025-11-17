<?php

namespace App\Models;

use App\Enums\ReportJourneyType;
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
        'finish_time' => 'datetime',
    ];

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
