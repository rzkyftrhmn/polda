<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportEvidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_journey_id',
        'report_id',
        'file_url',
        'file_type',
    ];

    public function journey()
    {
        return $this->belongsTo(ReportJourney::class, 'report_journey_id');
    }
}
