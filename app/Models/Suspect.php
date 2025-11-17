<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suspect extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'name',
        'division_id',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
    
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
}
