<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventUnitProof extends Model
{
    use HasFactory;
    protected $table = 'event_unit_proofs';
    protected $fillable = [
        'event_id',
        'user_id',
        'division_id',
        'file_path',
        'file_type',
        'description',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
