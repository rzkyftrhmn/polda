<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventUnitProofFile extends Model
{
    use HasFactory;

    protected $table = 'event_unit_proof_files';
    protected $fillable = [
        'event_unit_proof_id',
        'file_path',
        'file_type',
    ];

    public function eventUnitProof()
    {
        return $this->belongsTo(EventUnitProof::class, 'event_unit_proof_id');
    }
}
