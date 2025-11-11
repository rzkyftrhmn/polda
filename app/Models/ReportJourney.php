<?php

namespace App\Models;

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

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    /** Relasi ke bukti pendukung */
    public function evidences()
    {
        return $this->hasMany(ReportEvidence::class, 'report_journey_id');
    }

    /** Relasi tambahan opsional (biar lengkap sesuai ERD) */
    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
}
