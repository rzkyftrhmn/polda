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
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Division::class, 'parent_id');
    }
}
