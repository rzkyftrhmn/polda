<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessData extends Model
{
    use HasFactory;
    protected $table = 'access_datas';
    protected $fillable = [
        'division_id',
        'report_id',
        'is_finish',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
    
    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }
}
