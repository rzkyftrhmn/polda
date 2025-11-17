<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'type',
        'level',
        'permissions',
    ];

    public function parent()
    {
        return $this->belongsTo(Division::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Division::class, 'parent_id');
    }
  
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function journeys()
    {
        return $this->hasMany(ReportJourney::class, 'institution_id');
    }

    public function report()
    {
        return $this->hasMany(Report::class, 'division_id');
    }

    public function access_datas()
    {
        return $this->hasMany(AccessData::class, 'division_id');
    }

    public function division()
    {
        return $this->hasMany(Suspect::class, 'division_id');
    }

}
