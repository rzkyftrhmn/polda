<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCategory extends Model
{
    use HasFactory;
    protected $table = 'report_categories';
    protected $fillable = [
        'name',
    ];

    public function reports()
    {
        return $this->hasMany(Report::class, 'category_id');
    }

}
